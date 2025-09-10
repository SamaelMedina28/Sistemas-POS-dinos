<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    public function cuts()
    {
        return $this->hasMany(Cut::class);
    }
}
