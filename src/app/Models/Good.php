<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'category',
        'condition',
        'name',
        'description',
        'price',
        'favorites_count',
        'user_id'
    ];
    
    // カテゴリーを配列に変換
    public function getCategoryArrayAttribute()
    {
        return explode(',', $this->category);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
