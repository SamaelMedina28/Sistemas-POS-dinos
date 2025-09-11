<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'product_count',
        'total_amount',
    ];
    // Relacion con los cortes
    public function cuts()
    {
        return $this->hasMany(Cut::class);
    }

    // Relacion con las ventas
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
