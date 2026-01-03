<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    // INI YANG KETINGGALAN TADI
    protected $fillable = [
        'date',
        'description',
        'category',
        'amount'
    ];
}
