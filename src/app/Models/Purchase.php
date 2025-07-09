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

    public function isSold()
    {
        return $this->good->is_sold ?? false;
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function evaluationBy($userId)
    {
        return $this->hasOne(Evaluation::class)->where('from_user_id', $userId);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
