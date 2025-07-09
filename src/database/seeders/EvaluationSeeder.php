<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Evaluation;
use App\Models\Purchase;

class EvaluationSeeder extends Seeder
{
    public function run(): void
    {
        $comments = [
            'とても丁寧でした！',
            '迅速な対応ありがとうございました。',
            '安心して取引できました。',
            'また機会があればよろしくお願いします。',
            '丁寧な対応でした。ありがとうございました！'
        ];

        $purchases = Purchase::with('transaction')->take(5)->get();

        foreach ($purchases as $purchase) {
            $transaction = $purchase->transaction;
            $sellerId = optional($transaction)->seller_id;

            if (!$transaction || !$sellerId) continue;

            Evaluation::create([
                'purchase_id'    => $purchase->id,
                'transaction_id' => $transaction->id, // 明示的に入れる
                'from_user_id'   => $purchase->buyer_id,
                'to_user_id'     => $sellerId,
                'rating'         => rand(3, 5),
                'comment'        => $comments[array_rand($comments)],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}

