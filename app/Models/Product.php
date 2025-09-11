<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'image_path',
        'type_id',
    ];

    // Relacion uno a unos con types
    public function type(){
        return $this->belongsTo(Type::class);
    }

    // relacion muchos a muchos con sales
    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'sale_details', 'product_id', 'sale_id')->withPivot('original_price', 'original_name', 'original_minutes');
    }
}
