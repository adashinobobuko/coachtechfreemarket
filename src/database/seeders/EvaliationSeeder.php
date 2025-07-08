<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Evaluation;
use App\Models\Purchase;

class EvaluationSeeder extends Seeder
{
    public function run(): void
    {
        // いくつかの購入データに評価を付ける（例：最大5件）
        $purchases = Purchase::with('transaction')->take(5)->get();

        foreach ($purchases as $purchase) {
            if (!$purchase->transaction) continue;

            Evaluation::create([
                'purchase_id' => $purchase->id,
                'from_user_id' => $purchase->buyer_id,                   // 購入者が評価
                'to_user_id'   => $purchase->transaction->seller_id,     // 出品者が評価される
                'rating'       => rand(3, 5),
                'comment'      => '丁寧な対応でした。ありがとうございました！',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}
