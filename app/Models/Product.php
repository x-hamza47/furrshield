<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'price', 'stock_quantity'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }
    public function shelter()
    {
        return $this->belongsTo(Shelter::class);
    }
}
