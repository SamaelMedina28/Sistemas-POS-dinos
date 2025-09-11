<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    /** @use HasFactory<\Database\Factories\SaleFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'time',
        'product_count',
        'lot_id',
    ];

    // relacion con lote
    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    // relacion con el pago
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
