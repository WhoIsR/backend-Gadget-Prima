<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'amount',
        'current_stock',
        'reason'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
