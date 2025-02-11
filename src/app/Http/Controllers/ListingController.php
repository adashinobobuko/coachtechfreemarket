<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Good;
use App\Models\User;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    public function showSellForm()
    {
        $goods = Good::all(); // 商品データを取得
        return view('listing', compact('goods'));
    }

    public function store(ExhibitionRequest $request)
    {
        //デバッグ用: リクエストされたデータを確認
        //dd($request->all());

        // 画像アップロード処理
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('goods', 'public'); // storage/app/public/goods に保存
        } else {
            return back()->with('error', '画像がアップロードされていません');
        }

        // 商品データを保存
        $goods = Good::create([
            'user_id' => Auth::id(),
            'image' => $request->file('image')->store('goods', 'public'),
            'category' => implode(',', $request->category),
            'condition' => $request->condition,
            'name' => $request->name,
            'brand' => $request->brand,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        return redirect()->route('index')->with('success', '商品が出品されました');
    }

}
