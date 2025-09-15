<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cut extends Model
{
    protected $fillable = [
        'type',
        'date',
        'time',
        'product_count',
        'total_amount',
        'lot_id',
    ];
    // relacion con lote
    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    // relacion con cut detail
    public function cutDetail()
    {
        return $this->hasOne(CutDetail::class);
    }

    protected $casts = [
        'date' => 'datetime',
        'time' => 'datetime',
    ];
}
