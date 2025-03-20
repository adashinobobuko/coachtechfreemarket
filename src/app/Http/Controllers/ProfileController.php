<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Good;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Favorite;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

    // プロフィール設定画面を表示
    public function edit()
    {
        //登録ユーザーの情報を取得
        $user = Auth::user();

        return view('mypage/profile', compact('user'));
    }

    public function imgupdate(ProfileRequest $request)
    {
        
        $user = Auth::user();

        if ($request->hasFile('profile_image')) {
            // 画像のバリデーション
            $request->validate([
                'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // 画像を保存
            $path = $request->file('profile_image')->store('profile_images', 'public');

            // 古い画像を削除（必要なら）
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // プロフィール画像のパスを更新
            $user->profile_image = $path;
            $user->save();
        }


        return back()->with('success', 'プロフィール画像が更新されました。');
    }


    // プロフィール情報を更新
    public function update(AddressRequest $request)
    {
        $user = Auth::user();

        $user->update($request->only('name','postal_code', 'address', 
        'building_name'));

        // プロフィールが初回なら `profile_completed` を `true` にする
        if (!$user->profile_completed) {
            $user->profile_completed = true;
            $user->save();
        }

        return redirect()->route('profile.edit')->with('success', 'プロフィールが更新されました。');
    }

    // 「出品した商品」タブ（出品した商品マイページ）
    public function sell()
    {
        $goods = Good::where('user_id', Auth::id())->get(); // ログインユーザーの商品取得
        $activeTab = 'sell';// デフォルトの値を定義
        $favorites = Auth::check() ? Favorite::where('user_id', Auth::id())->with('good')->get() : collect();

        return view('mypage.mypage', [
            'goods' => $goods,
            'favorites' => $favorites,
            'activeTab' => 'sell'
        ]);
    }

    // 「購入した商品」タブ（購入した商品ページ）
    public function buy()
    {
        $purchases = Purchase::where('user_id', auth()->id())->with('good')->get();
        $activeTab = 'buy';// 購入した商品リストをアクティブにする
        $favorites = Auth::check() ? Favorite::where('user_id', Auth::id())->with('purchase')->get() : collect();

        return view('mypage.mypage', [
            'purchases' => $purchases,
            'favorites' => $favorites,
            'activeTab' => 'buy'
        ]);
    }
}
