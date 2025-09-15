<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleRequest;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use App\Services\SaleService;

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
        return DB::transaction(function () use ($request) {
            $products = Product::whereIn('id', $request->products)->get();// ? Traemos los productos vendidos
            $total = $this->saleService->calculateTotal($products);// ? Calculamos el total por los productos
            // ? Validamos que el pago sea suficiente
            try {
                $this->saleService->validatePayment($request, $total);
            } catch (\Exception $e) {
                return response()->json([
                    'error'        => $e->getMessage(),
                    'total_to_pay' => $total,
                    'difference'   => ($request->cash ?? 0) + ($request->card ?? 0) - $total,
                ], 422);
            }
            
            $lot = $this->saleService->createLot();
            $sale = $this->saleService->createSale($lot); // ? Creamos la venta y la asociamos al lote
            $this->saleService->associateProducts($sale, $products); // ? Asociamos los productos a la venta
            $sale->payment()->create($this->saleService->preparePaymentData($request, $total)); // ? Creamos la informacion del pago
            // Actualizamos el lote con la cantidad de productos y el total
            $lot->increment('product_count', count($products)); 
            $lot->increment('total_amount', $total); 
            $lot->increment('cash', $sale->payment->cash);
            $lot->increment('card', $sale->payment->card);

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
