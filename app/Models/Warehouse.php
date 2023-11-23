<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    protected $fillable = ['brand','name','quantity','price', 'amount', 'supplier_id'];

    public function suppliers(): BelongsTo 
    {
        return $this->belongsTo(Supplier::class);
    }

}
