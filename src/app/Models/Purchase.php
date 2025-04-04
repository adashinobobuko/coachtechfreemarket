<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'good_id', 'payment_method', 'address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function good()
    {
        return $this->belongsTo(Good::class);
    }

    public function favorite()
    {
        return $this->belongsTo(Favorite::class);
    }

    public function isSold()
    {
        return $this->good->is_sold ?? false;
    }
      
}
