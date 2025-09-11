<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = [
        'name',
        'price',
        'minutes',
        'description',
    ];
    // Relacion uno a uno con products
    public function products(){
        return $this->hasMany(Product::class);
    }
}
