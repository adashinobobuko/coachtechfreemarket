<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'purchase_id',
        'buyer_id',
        'seller_id',
        'good_id',
        'status',
    ];

    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function good()
    {
        return $this->belongsTo(Good::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function messages()
    {
        return $this->hasMany(TransactionMessage::class);
    }

    public function evaluation()
    {
        return $this->hasOne(Evaluation::class);
    }

    public function unreadMessagesBy($userId)
    {
        return $this->messages()
            ->where('recipient_id', $userId)
            ->where('is_read', false);
    }
}
