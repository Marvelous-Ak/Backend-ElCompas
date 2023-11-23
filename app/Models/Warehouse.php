<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Warehouse extends Model
{
    use HasFactory;
    protected $fillable = ['brand','name','quantity','price', 'amount', 'supplier_id'];

    public function supplier(): BelongsTo 
    {
        return $this->belongsTo(Supplier::class);
    }

}
