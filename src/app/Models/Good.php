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
        'brand',
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
    //コメント関連
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    //ユーザーとの紐づけ
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //いいね、マイリスト関連
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'good_id');
    }

    public function getFavoritesCountAttribute()
    {
        return $this->favorites()->count();
    }

    public function isFavoritedBy($user)
    {
        return $this->favorites()->where('user_id', $user->id)->exists();
    }
    
    //検索機能のローカルスコープ
    public function scopeKeywordSearch($query, $keyword)
    {
    if (!empty($keyword)) {
        $query->where('name', 'like', '%' . $keyword . '%');
    }
    }

}
