<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'image_path',
        'type_id',
    ];

    // Relacion uno a unos con types
    public function type(){
        return $this->belongsTo(Type::class);
    }
}
