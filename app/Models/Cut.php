<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cut extends Model
{
    // relacion con lote
    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    // relacion con cut details
    public function cutDetails()
    {
        return $this->hasMany(CutDetail::class);
    }
}
