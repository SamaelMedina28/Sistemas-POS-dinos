<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // ? Iniciamos la transaccion (esto es para que si algo falla se deshaga todo)
        DB::transaction(function () use ($request) {
            // ? Traemos los productos
            $products = Product::whereIn('id', $request->products)->get();
            // ? Calculamos el total
            $total = $products->sum('type.price');
            // ? Validamos que el pago sea suficiente
            if ($request->card + $request->cash < $total) {
                return response()->json([
                    'error' => 'El pago no es suficiente',
                    'method' => $request->method,
                    'cash' => $request->cash,
                    'card' => $request->card,
                    'total_send' => $request->card + $request->cash,
                    'total_to_pay' => $total,
                    'difference' => $request->card + $request->cash - $total,
                ], 422);
            }

            // ? Validaciones
            $this->validateSale($request);
            // ? Checamos si existe algun lote
            $lot = $this->createLot();
            // ? Creamos la venta y la asociamos al lote
            $sale = Sale::create([
                'date' => now(),
                'time' => now(),
                'lot_id' => $lot->id,
            ]);
            // ? Asociamos los productos a la venta
            $this->associateProducts($sale, $products);
            // ? Creamos la informacion del pago
            $sale->payment()->create([
                'method' => $request->method,
                'cash' => $request->cash ?? 0,
                'card' => $request->card ?? 0,
                'total' => $total,
            ]);
            // ? Actualizamos el lote con la cantidad de productos y el total
            $lot->update([
                'product_count' => $lot->product_count + count($request->products),
                'total_amount' => $lot->total_amount + $products->sum('type.price'),
            ]);
            // ? Devolvemos la venta con los productos y el cambio para el cliente
            return response()->json([
                'change' => $request->cash + $request->card - $total,
                'sale' => $sale->load('products'),
            ]);
        });
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

    private function validateSale(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*' => 'required|exists:products,id',
            'method' => 'required|in:cash,card,mix',
            'cash' => 'required_if:method,cash|required_if:method,mix|numeric',
            'card' => 'required_if:method,card|required_if:method,mix|numeric',
        ], [
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
    }

    private function associateProducts(Sale $sale, $products)
    {
        foreach ($products as $product) {
            $sale->products()->attach($product->id, [
                'original_name' => $product->name,
                'original_price' => $product->type->price,
                'original_minutes' => $product->type->minutes,
            ]);
        }
    }

    private function createLot()
    {
        $lot = Lot::latest()->first();
        if (!$lot) {
            // ? Si no existe ningun lote creamos uno
            $lot = Lot::create([
                'date' => now(),
                'start_time' => now(),
                'end_time' => null,
                'product_count' => 0,
                'total_amount' => 0.0,
            ]);
        }
        return $lot;
    }
}
