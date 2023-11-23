<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['brand','name','image','promo','price', 'pricePromo', 'description', 'stock', 'cost_of_sale'];


    /// Métodos de relaciones
    public function catalogs(): BelongsToMany
    {
        return $this->belongsToMany(Catalog::class, 'catalog_product');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorities_list');
    }

    public function shoppingCarts(): BelongsToMany
    {
        return $this->belongsToMany(ShoppingCart::class, 'product_shopping_cart');
    }
    

}
