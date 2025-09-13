<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class SaleController extends Controller
{

    // Este metodo sera casi de prueba ya que nunca se deberia requerir el recuperar TODAS las ventas
    public function index()
    {
        return response()->json([
            'sales' => Sale::orderBy('id', 'desc')->with('payment', 'products.type')->paginate(10)
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*' => 'required|exists:products,id',
            'method' => 'required|in:cash,card,mix',
            'cash' => 'required_if:method,cash|required_if:method,mix|numeric',
            'card' => 'required_if:method,card|required_if:method,mix|numeric',
        ],[
            'products.*.required' => 'El producto es requerido',
            'products.*.exists' => 'El producto no existe',
            'method.required' => 'El metodo es requerido',
            'method.in' => 'El metodo no es valido',
            'cash.required_if' => 'El efectivo es requerido',
            'cash.numeric' => 'El efectivo debe ser un numero',
            'card.required_if' => 'La tarjeta es requerida',
            'card.numeric' => 'La tarjeta debe ser un numero',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        /* 
            checamos si existe algun lota, si no existe ninguno creamos uno y asociamos la venta a ese, si existen loten, agarramos el ultimo lote y asociamos la venta a ese
         */
        $lot = Lot::latest()->first();
        if (!$lot) {
            $lot = Lot::create([
                'date' => now(),
                'start_time' => now(),
                'end_time' => null,
                'product_count' => 0,
                'total_amount' => 0.0,
            ]);
        }
        $sale = Sale::create([
            'date' => now(),
            'time' => now(),
            'lot_id' => $lot->id,
        ]);
        foreach ($request->products as $id) {
            $product = Product::find($id);
            $sale->products()->attach($product->id, [
                'original_name' => $product->name,
                'original_price' => $product->type->price,
                'original_minutes' => $product->type->minutes,
            ]);
            $lot->update([
                'product_count' => $lot->product_count + 1,
                'total_amount' => $lot->total_amount + $product->type->price,
            ]);
        }
        $sale->payment()->create([
            'method' => $request->method,
            'cash' => $request->cash ?? 0,
            'card' => $request->card ?? 0,
            'total' => $request->cash + $request->card,
        ]);
        return response()->json([
            'sale' => $sale->load('payment', 'products'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        if (!$sale) {
            return response()->json([
                'error' => 'This sale does not exist'
            ], 404);
        }
        $sale->load('payment', 'products.type');
        return response()->json([
            'sale' => $sale
        ]);
    }
}
