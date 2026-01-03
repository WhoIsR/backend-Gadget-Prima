<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['invoice_number', 'user_id', 'total_amount', 'tax', 'payment_method'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
