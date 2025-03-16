<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasesAddress extends Model
{
    use HasFactory;

    protected $fillable = ['good_id', 'postal_code', 'address', 'building_name'];

    //グッズIDとのリレーション
    public function good()
    {
        return $this->belongsTo(Good::class, 'good_id', 'id');
    }

}