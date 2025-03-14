<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasesAddress extends Model
{
    use HasFactory;

    protected $fillable = ['good_id', 'postal_code', 'address', 'building_name'];

    // // ユーザーとのリレーション（1ユーザーが複数の住所を持つ）
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
    
    //グッズIDとのリレーション
    public function good()
    {
        return $this->belongsTo(good::class);
    }
}
