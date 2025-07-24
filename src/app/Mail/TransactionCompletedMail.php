<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $good;
    public $purchase;
    public $address;

    public function __construct(User $user, Transaction $transaction)
    {
        $this->user     = $user; // 出品者（メールの宛名用）
        $this->good     = $transaction->purchase->good;
        $this->purchase = $transaction->purchase;

        $buyer = $transaction->purchase->user; // ← 購入者（配送先用）

        $this->address  = (object)[
            'name'    => $transaction->purchase->address_name ?? $buyer->name,
            'address' => $transaction->purchase->address ?? '不明',
        ];
    }

    public function build()
    {
        return $this->view('emails.transaction-completed')
                    ->with([
                        'user'     => $this->user,       // ←出品者の名前として使える
                        'good'     => $this->good,
                        'purchase' => $this->purchase,
                        'address'  => $this->address,
                    ]);
    }
}
