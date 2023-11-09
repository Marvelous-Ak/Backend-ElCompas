<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;
use App\User;

class CommentProduct extends Model
{
    protected $fillable = ['id' ,'product_id', 'comment', 'user_id', 'user_name', 'user_image','rating'];
    protected $table = 'comments_products';
    
    public function product()
        {
            return $this->belongsTo(Product::class);
        }

    public function user()
        {
            return $this->belongsTo(User::class);
        }
}
