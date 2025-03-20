<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'image',
        'category',
        'brand',
        'condition',
        'name',
        'description',
        'price',
        'favorites_count',
        'user_id',
        'is_sold'
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

    public function purchase()
    {
        return $this->hasOne(Purchase::class, 'good_id');
    }

    public function getFavoritesCountAttribute()
    {
        return $this->attributes['favorites_count'];
    }

    public function isFavoritedBy($user)
    {
        return $this->favorites()->where('user_id', $user->id)->exists();
    }
    
    //検索機能のローカルスコープ
    public function scopeKeywordSearch($query, $keyword)
    {
        if (!empty($keyword)) {
            return $query->where('name', 'like', '%' . $keyword . '%');
        };
        return $query;
    }

    //売り切れ処理
    public function isSold()
    {
        return (bool) $this->is_sold; // 明示的に boolean に変換
    }

    public function purchasesAddresses()
    {
        return $this->hasMany(PurchasesAddress::class, 'good_id', 'id');
    }
}
