<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'sku',
        'category',
        'brand',
        'buy_price',
        'price',
        'stock',
        'min_stock', 
        'image',
        'status'
    ];
}
