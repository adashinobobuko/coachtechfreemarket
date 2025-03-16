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
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => '商品名', // ここを `$good->name` に変更可
                        ],
                        'unit_amount' => 1500 * 100, // `$good->price * 100` に変更 仮の名前と価格なので余力があれば記載
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => url('/success'),
                'cancel_url' => url('/cancel'),
            ]);

            return response()->json(['sessionId' => $session->id]);

        } catch (\Exception $e) {
            return response()->json(['error' => '決済セッションの作成に失敗しました。'], 500);
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
