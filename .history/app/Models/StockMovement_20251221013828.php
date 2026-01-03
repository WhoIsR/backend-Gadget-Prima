<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
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
