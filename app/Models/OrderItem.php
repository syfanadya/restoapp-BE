<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class orderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quantity',
        'price',
        'order_id',
        'food_id',
    ];

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
