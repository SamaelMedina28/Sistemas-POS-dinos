<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CutDetail extends Model
{
    public function cut()
    {
        return $this->belongsTo(Cut::class);
    }
}
