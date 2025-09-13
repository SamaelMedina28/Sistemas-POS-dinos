<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleRequest;
use App\Models\Lot;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\SaleService;
use PhpParser\Node\Stmt\TryCatch;

class SaleController extends Controller
{
    public function __construct(private SaleService $saleService)
    {
    }

    // Este metodo sera casi de prueba ya que nunca se deberia requerir el recuperar TODAS las ventas
    public function index()
    {
        return response()->json([
            'sales' => Sale::orderBy('id', 'desc')->with('payment', 'products.type')->paginate(10)
        ]);
    }
    /**
     * Store a newly created resource in storage.
     * 
     * @param  \App\Http\Requests  $request
     */
    public function store(SaleRequest $request)
    {
        // ? Validaciones
        /* $validator = Validator::make($request->all(), [
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
        } */

        return DB::transaction(function () use ($request) {
            // ? Traemos los productos
            $products = Product::whereIn('id', $request->products)->get();
            // ? Calculamos el total
            $total = $this->saleService->calculateTotal($products);
            // ? Validamos que el pago sea suficiente
            try {
                $this->saleService->validatePayment($request, $total);
            } catch (\Exception $e) {
                return response()->json([
                    'error'        => $e->getMessage(),
                    'total_to_pay' => $total,
                    'total_send'   => ($request->cash ?? 0) + ($request->card ?? 0),
                    'difference'   => ($request->cash ?? 0) + ($request->card ?? 0) - $total,
                    'cash'         => $request->cash ?? 0,
                    'card'         => $request->card ?? 0,
                    'method'       => $request->method,
                ], 422);
            }
            // ? Checamos si existe algun lote
            $lot = $this->saleService->createLot();
            // ? Creamos la venta y la asociamos al lote
            $sale = $this->saleService->createSale($lot);
            // ? Asociamos los productos a la venta
            $this->saleService->associateProducts($sale, $products);
            // ? Creamos la informacion del pago
            $sale->payment()->create($this->saleService->preparePaymentData($request, $total));
            // ? Actualizamos el lote con la cantidad de productos y el total
            $lot->increment('product_count', count($products));
            $lot->increment('total_amount', $total);
            // ? Devolvemos la venta con los productos y el cambio para el cliente
            return response()->json([
                'sale' => $sale->load('products', 'payment'),
            ]);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sale = Sale::find($id);
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
