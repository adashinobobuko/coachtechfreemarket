<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Good;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    public function store(PurchaseRequest $request)
    {
        $user = Auth::user();
        $paymentMethod = $request->payment_method;

        DB::beginTransaction();
        try {
            Purchase::create([
                'user_id' => $user->id,
                'good_id' => $request->good_id,
                'payment_method' => $paymentMethod,
                'address' => $user->address
            ]);

            $good = Good::find($request->good_id);
            if ($good) {
                $good->update(['is_sold' => true]);
            }

            DB::commit();

            return redirect()->route('index')->with(['message' => '購入が完了しました', 'activeTab' => 'recommend']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('購入処理エラー: ' . $e->getMessage());
            return redirect()->route('index')->with('error', '購入処理中にエラーが発生しました。');
        }
    }

    // 購入完了処理
    public function complete($session_id)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    
        try {
            $session = CheckoutSession::retrieve($session_id);
    
            $goodId = $session->metadata->good_id ?? null;
            $userId = $session->metadata->user_id ?? null;
            $paymentMethod = $session->metadata->payment_method ?? 'カード払い';
    
            if (!$goodId || !$userId) {
                return redirect()->route('index')->with('error', '購入情報が不足しています');
            }
    
            $user = User::find($userId);
            if (!$user) {
                return redirect()->route('index')->with('error', 'ユーザーが見つかりません');
            }
    
            DB::beginTransaction();
    
            Purchase::create([
                'user_id' => $user->id,
                'good_id' => $goodId,
                'payment_method' => $paymentMethod,
                'address' => $user->address
            ]);
    
            $good = Good::find($goodId);
            if ($good) {
                $good->update(['is_sold' => true]);
            }
    
            DB::commit();
    
            return redirect()->route('index')->with('message', '購入が完了しました');
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('購入完了処理エラー: ' . $e->getMessage());
            return redirect()->route('index')->with('error', '購入処理中にエラーが発生しました');
        }
    }
    
}

