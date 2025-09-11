<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CutDetail extends Model
{
    protected $fillable = [
        'cash',
        'card',
        'cash_total',
        'card_total',
        'total',
        'cash_difference',
        'card_difference',
        'total_difference',
    ];
    public function cut()
    {
        return $this->belongsTo(Cut::class);
    }
}
