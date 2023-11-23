<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    use HasFactory;

    protected $fillable = ['total_cost'];

    //Relación 1a1: Users...
    public function user(): BelongsTo ///Un carrito le pertenece a un Usuario
    {
        return $this->belongsTo(User::class);
    }
    //Relación NaN: Products
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_shopping_cart')->withPivot('quantity');
    }
}
