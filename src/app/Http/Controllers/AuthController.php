<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
//use App\Http\Requests\RegisterRequest;
//use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    //
        /**
     * 登録フォームの表示
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

        /**
     * ユーザー登録処理
     */
    public function register(RegisterRequest $request)
    {
        // バリデーションはRegisterRequestで行う

        // ユーザーを作成
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 登録後、ログイン画面にリダイレクト
        return redirect('/login')->with('message', '登録が完了しました！');
    }

    // ログインフォームを表示
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ログイン処理
    public function login(LoginRequest $request) // LoginRequestを適用
    {
        // バリデーション済みデータを取得
        $credentials = $request->validated();

        // 認証処理
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // 管理ページ(admin)にリダイレクト
            return redirect()->intended('/admin');
        }

        // 認証失敗時
        return back()->withErrors([
            'email' => 'メールアドレスまたはパスワードが正しくありません。',
        ])->onlyInput('email');
    }

    // ログアウト処理
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}