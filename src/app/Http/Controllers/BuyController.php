<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Good;
use App\Models\PurchasesAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


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

    public function showForm($goodsid) // goodsidをルートパラメータとして受け取る
    {
        $user = Auth::user();

        // 指定されたgoodsidを取得
        $good = Good::with('purchasesAddresses')->find($goodsid);

        // purchasesAddressesがnullのときの対策
        if ($good) {
            $good->loadMissing('purchasesAddresses');
        }

        return view('goods.address-change', compact('user', 'good'));
    }

    public function updateAddress(Request $request)
    {
        $request->validate([
            'goodsid' => 'required|exists:goods,id',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
        ]);

        $goodsid = $request->input('goodsid');

        // 商品に関連する購入履歴の住所を更新
        $good = Good::find($goodsid);
        if ($good && $good->purchasesAddresses->isNotEmpty()) {
            $purchaseAddress = $good->purchasesAddresses->first();
            $purchaseAddress->update([
                'postal_code' => $request->input('postal_code'),
                'address' => $request->input('address'),
                'building_name' => $request->input('building_name'),
            ]);
        }

        // 修正: goodsid を渡してリダイレクト
        return redirect()->route('buy.show', ['id' => $goodsid])->with('success', '住所を変更しました');
    }

}
