<?php

namespace App\Services;

use App\Models\Lot;
use App\Models\Sale;


class SaleService
{
  public function calculateTotal($products)
  {
    return $products->sum('type.price');
  }

  // ? Validamos que el pago sea mayor o igual al total
  public function validatePayment($request, $total)
  {
    $paid = ($request->cash ?? 0) + ($request->card ?? 0);
    if ($paid < $total) {
      throw new \Exception("Pago insuficiente");
    }
    return $paid;
  }

  // ? Preparamos los datos del pago
  public function preparePaymentData($request, $total)
  {
    $cash = max(0, $total - ($request->card ?? 0));
    $card = $request->card ?? 0;
    $change = ($request->cash ?? 0) - ($total - ($request->card ?? 0));
    return [
      'method' => $request->method,
      'cash'   => $cash,
      'card'   => $card,
      'change' => $change,
      'total'  => $total,
    ];
  }

  // ? Creamos un nuevo lote
  public function createLot(): Lot
  {
    $lot = Lot::latest()->first();
    if (!$lot) {
      $lot = Lot::create([
        'date'          => now(),
        'start_time'    => now(),
        'end_time'      => null,
        'product_count' => 0,
        'total_amount'  => 0.0,
      ]);
    }
    return $lot;
  }

  // ? Creamos una nueva venta
  public function createSale(Lot $lot): Sale
  {
    return Sale::create([
      'date'   => now(),
      'time'   => now(),
      'lot_id' => $lot->id,
    ]);
  }

  // ? Asociamos los productos a la venta
  public function associateProducts(Sale $sale, $products)
  {
    foreach ($products as $product) {
      $sale->products()->attach($product->id, [
        'original_name'    => $product->name,
        'original_price'   => $product->type->price,
        'original_minutes' => $product->type->minutes,
      ]);
    }
  }
}
