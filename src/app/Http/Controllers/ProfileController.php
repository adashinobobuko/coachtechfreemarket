<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    // プロフィール設定画面を表示
    public function edit()
    {
        $user = Auth::user();
        return view('mypage/profile', compact('user'));
    }

    // プロフィール情報を更新
    public function update(ProfileRequest $request)
    {
        // $request->validate([
        //     'profile_image' => 'nullable|image|max:2048', // 画像は最大2MB
        //     'postal_code' => 'nullable|string|max:10',
        //     'address' => 'nullable|string|max:255',
        //     'building_name' => 'nullable|string|max:255',
        // ]);バリデーションはあとでリクエストファイルで行う

        $user = Auth::user();

        // プロフィール画像のアップロード処理
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        // 他のフィールドを更新
        $user->update($request->only('postal_code', 'address', 'building_name'));

        return redirect()->route('profile.edit')->with('success', 'プロフィールが更新されました。');
    }

    public function index()
    {
        return view('mypage/mypage');
    }
}
