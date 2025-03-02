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

    public function showForm()
    {
        $user = Auth::user();
        $goodId = session('last_good_id');

        // $goodId が null の場合に備えて $good を null 許容
        $good = $goodId ? Good::with('purchasesAddresses')->find($goodId) : null;

        // purchasesAddresses が null の場合でも空のコレクションを返す
        if ($good) {
            $good->loadMissing('purchasesAddresses');
        }

        return view('goods.address-change', compact('user', 'good'));
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
        $goodId = session('last_good_id'); // セッションから取得

        if (!$goodId) {
            return redirect()->route('address.change.form')->withErrors('商品が選択されていません。');
        }

        // `$good` をデータベースから取得
        $good = Good::find($goodId);

        if (!$good) {
            return redirect()->route('address.change.form')->withErrors('指定された商品が存在しません。');
        }

        // ログで `good_id` を確認
        \Log::info('Good ID: ' . ($good->id ?? 'NULL'));

        // 新しい購入先住所を追加（複数登録可能）
        $address = PurchasesAddress::create([
            'good_id' => $good->id,  // ここで `good_id` が null でないか確認
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'building_name' => $request->building_name
        ]);

        // セッションデータの処理
        session()->forget('last_good_id');

        return redirect()->route('buy.show', ['id' => $good->id])
            ->with('success', '住所が更新されました。');
    }

}
