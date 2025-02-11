<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Good;
use App\Models\User;

class ProfileController extends Controller
{

    // プロフィール設定画面を表示
    public function edit()
    {
        //登録ユーザーの情報を取得
        $user = Auth::user();

        return view('mypage/profile', compact('user'));
    }

    // プロフィール情報を更新
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        // プロフィール画像のアップロード処理
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        // 他のフィールドを更新
        $user->update($request->only('name','postal_code', 'address', 'building_name'));

        return redirect()->route('profile.edit')->with('success', 'プロフィールが更新されました。');
    }

    public function showMypage()
    {
        $user = Auth::user();
        $goods = Good::where('user_id', $user->id)->get();// 商品を取得（関係がある場合）

        return view('mypage.mypage', compact('goods'));

    }
}
