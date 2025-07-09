<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionMessage;
use App\Models\User;
use App\Models\Good;
use App\Models\Purchase;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // 既存ユーザーを取得（ItemSeederで作成済み）
        $sellerA = User::find(1); // User One
        $sellerB = User::find(2); // User Two
        $buyer = User::find(3);   // User Three（購入専用ユーザー）

        if (!$sellerA || !$sellerB || !$buyer) {
            echo "ユーザーが不足しています。Seederを確認してください。\n";
            return;
        }

        // sellerAの商品（ID昇順で5件）を取得
        $goodsA = Good::where('user_id', $sellerA->id)->take(2)->get();

        // sellerBの商品（ID昇順で5件）を取得
        $goodsB = Good::where('user_id', $sellerB->id)->take(2)->get();

        $allGoods = $goodsA->merge($goodsB);

        foreach ($allGoods as $good) {
            // 購入データ作成
            $purchase = Purchase::create([
                'user_id' => $buyer->id,
                'buyer_id' => $buyer->id,
                'good_id' => $good->id,
                'payment_method' => 'card',
                'address' => '東京都架空市1-1-1',
            ]);

            $good->update(['is_sold' => true]);

            // 取引データ作成
            $transaction = Transaction::create([
                'purchase_id' => $purchase->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $good->user_id,
                'good_id' => $good->id,
                'status' => 'in_progress',
            ]);

            // purchaseにtransaction_idを保存
            $purchase->update([
                'transaction_id' => $transaction->id,
            ]);

            // チャットメッセージ（ダミー2件）
            TransactionMessage::create([
                'purchase_id' => $purchase->id,
                'transaction_id' => $transaction->id,
                'user_id' => $buyer->id,
                'recipient_id' => $good->user_id,
                'message' => '購入しました。よろしくお願いします。',
                'is_read' => false,
            ]);

            TransactionMessage::create([
                'purchase_id' => $purchase->id,
                'transaction_id' => $transaction->id,
                'user_id' => $good->user_id,
                'recipient_id' => $buyer->id,
                'message' => 'ありがとうございます。発送いたします！',
                'is_read' => false,
            ]);
        }
    }
}
