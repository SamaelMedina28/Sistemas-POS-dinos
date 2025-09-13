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

  public function validatePayment($request, $total)
  {
    $paid = ($request->cash ?? 0) + ($request->card ?? 0);
    if ($paid < $total) {
      throw new \Exception("Pago insuficiente");
    }
    return $paid;
  }

  public function preparePaymentData($request, $total)
  {
    return [
      'method' => $request->method,
      'cash'   => max(0, $total - ($request->card ?? 0)),
      'card'   => $request->card ?? 0,
      'change' => ($request->cash ?? 0) - ($total - ($request->card ?? 0)),
      'total'  => $total,
    ];
  }

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

  public function createSale(Lot $lot): Sale
  {
    return Sale::create([
      'date'   => now(),
      'time'   => now(),
      'lot_id' => $lot->id,
    ]);
  }

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
