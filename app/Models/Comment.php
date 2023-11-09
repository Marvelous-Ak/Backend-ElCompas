<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = ['id' ,'content', 'user_id', 'user_name', 'user_image','rating'];
    protected $table = 'comments';
    use HasFactory;
    public function users(): BelongsTo
    {
        return $this->belongsTo(users::class);
    }
}
