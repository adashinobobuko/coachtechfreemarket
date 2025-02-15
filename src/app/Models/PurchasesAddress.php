<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasesAddress extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'postal_code', 'address', 'building_name'];

    // ユーザーとのリレーション（1ユーザーが複数の住所を持つ）
    public function user()
    {
        return $this->belongsTo(User::class);
    }    
}
