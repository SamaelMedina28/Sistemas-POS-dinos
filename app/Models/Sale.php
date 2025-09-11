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

    // relacion muchos a muchos con products
    public function products()
    {
        return $this->belongsToMany(Product::class, 'sale_details', 'sale_id', 'product_id')->withPivot('original_price', 'original_name', 'original_minutes');
    }
}
