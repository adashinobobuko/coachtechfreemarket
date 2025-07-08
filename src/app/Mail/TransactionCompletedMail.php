<?php

namespace App\Mail;

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

    public function __construct(Transaction $transaction)
    {
        $this->user     = $transaction->purchase->user;
        $this->good     = $transaction->purchase->good;
        $this->purchase = $transaction->purchase;
        $this->address  = (object)[
            'name'    => $transaction->purchase->address_name ?? $this->user->name,
            'address' => $transaction->purchase->address ?? '不明'
        ];
    }

    public function build()
    {
        return $this->view('emails.transaction-completed')
                    ->with([
                        'user' => $this->user,
                        'good' => $this->good,
                        'purchase' => $this->purchase,
                        'address' => $this->address,
                    ]);
    }
}

