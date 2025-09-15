<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'method',
        'cash',
        'card',
        'change',
        'total',
        'sale_id',
    ];
    // Relacion con la venta
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
