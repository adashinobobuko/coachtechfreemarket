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
        $good = Good::find($id);

        if (!$good) {
            abort(404, '商品が見つかりません');
        }

        // 商品IDをセッションに保存
        session(['last_good_id' => $good->id]);

        return view('goods.goods-buy', compact('good'));
    }

    public function showForm()
    {
        return view('goods.address-change', ['user' => Auth::user()]);
    }

    public function updateAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'building_name' => 'nullable|string|max:30'
        ]);

        if ($validator->fails()) {
            return redirect()->route('address.change.form')
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();

        // 新しい購入先住所を追加（複数登録可能）
        $address = PurchasesAddress::create([
            'user_id' => $user->id,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'building_name' => $request->building_name
        ]);

        // セッションデータの処理
        $goodId = session('last_good_id');
        session()->forget('last_good_id');

        if ($goodId) {
            return redirect()->route('buy.show', ['id' => $goodId])
                ->with('success', '住所が更新されました。');
        } else {
            return redirect()->route('address.change.form')
                ->with('success', '住所が更新されました。');
        }
    }
}
