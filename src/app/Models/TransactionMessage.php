<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionMessage extends Model
{
    protected $fillable = [
        'transaction_id',
        'user_id',
        'message',
        'recipient_id',
        'is_read',
        'purchase_id', 
        'image_path',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
