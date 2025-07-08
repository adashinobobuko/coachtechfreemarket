<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Good;
use App\Models\PurchasesAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;

class BuyController extends Controller
{
    public function showBuyform($id)
    {
        $good = Good::with('purchasesAddresses')->findOrFail($id);
        if (!$good) {
            abort(404, '商品が見つかりません');
        }

        // 商品IDをセッションに保存
        session(['last_good_id' => $good->id]);

        return view('goods.goods-buy', compact('good'));
    }

    public function processCheckout(Request $request, $goodsid)
    {
        try {
            // 商品を取得
            $good = Good::findOrFail($goodsid);

            // Stripe APIキー設定
            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentMethod = $request->input('payment_method');

            $amount = (int) $good->price * 0.01 * 100;

            // Stripe セッション作成
            if ($paymentMethod === "カード払い") {
                // Stripe Checkout セッション作成 (クレジットカード)
                $session = Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'jpy',
                            'product_data' => ['name' => $good->name],
                            'unit_amount' => $amount,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => url('/purchase/complete') . '/{CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('buy.show', ['id' => $goodsid]),
                    'metadata' => [
                        'good_id' => $good->id,
                        'user_id' => Auth::id(),
                        'payment_method' => 'カード払い',
                    ],
                ]);

                return response()->json(['sessionId' => $session->id]);

            } elseif ($paymentMethod === "コンビニ払い") {
                $paymentIntent = PaymentIntent::create([
                    'amount' => $amount,
                    'currency' => 'jpy',
                    'payment_method_types' => ['konbini'],
                    'payment_method_data' => [
                        'type' => 'konbini',
                        'billing_details' => [
                            'name' => Auth::user()->name ?? 'ゲストユーザー',
                            'email' => Auth::user()->email ?? 'test@example.com',
                        ],
                        'konbini' => [],
                    ],
                    'metadata' => [ 
                        'good_id' => $good->id,
                        'user_id' => Auth::id(),
                        'payment_method' => 'コンビニ払い',
                    ],
                    'confirm' => true,
                    'confirmation_method' => 'automatic',
                    'capture_method' => 'automatic',
                ]);
                
                return response()->json(['client_secret' => $paymentIntent->client_secret]);
            }

            return response()->json(['error' => '支払い方法が無効です'], 400);

        } catch (\Exception $e) {
            Log::error('Stripe セッション作成エラー: ' . $e->getMessage());
            return response()->json([
                'error' => '決済処理中にエラーが発生しました：' . $e->getMessage()
            ], 500);
        }
    }

    public function showForm($goodsid)
    {
        $good = Good::find($goodsid);
        
        if (!$good) {
            abort(404, '商品が見つかりません');
        }

        return view('goods.address-change', compact('good'));
    }

    public function updateAddress(Request $request, $goodsid)
    {
        $request->validate([
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
        ]);
 
        $good = Good::findOrFail($goodsid);
        $user = Auth::user();

        // 既存の住所があるかチェック
        $address = $good->purchasesAddresses->first();

        if ($address) {
            // 住所を更新
            $address->update([
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building_name' => $request->building_name,
            ]);
        } else {
            // 新規作成
            PurchasesAddress::create([
                'good_id' => $good->id,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building_name' => $request->building_name,
            ]);
        }
        return redirect()->route('buy.show',['id' => $goodsid])->with('success', '住所を更新しました！');
    }

}
