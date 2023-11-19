<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['brand','name','image','promo','price', 'pricePromo', 'description', 'stock', 'cost_of_sale'];

    protected $name_suppliers;

    // Métodos de acceso y mutadores para el atributo suppliers
    public function getName_suppliers()
    {
        return $this->name_suppliers;
    }

    public function setName_suppliers($value)
    {
        $this->name_suppliers = $value;
    }

    /// Métodos de relaciones
    public function catalogs(): BelongsToMany
    {
        return $this->belongsToMany(Catalog::class, 'catalog_product');
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorities_list');
    }

    

}
