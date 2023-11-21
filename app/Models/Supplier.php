<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = ['email', 'phone', 'address', 'business'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_supplier');
    }
}
