<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * 登録フォームの表示
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * ログインフォームの表示
     */

    public function showLoginForm()
    {
        return view('auth.login');
    }


    /**
     * ユーザー登録処理（仮登録）
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // ✅ `email_verification_token` は `User` モデルの `boot()` で自動セットされる
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 認証メール送信
        Mail::send('emails.verify', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('メール認証のお願い');
        });
        //TODO:新しくタスクが追加された、ログインページに行くのではなく専用のページにいくように
        return redirect()->route('login')->with('message', '認証メールを送信しました！メールを確認してください。');
        //このメッセージは新しいページに表示されるように
    }

    /**
     * メール認証の処理
     */
    public function verifyEmail($token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', '無効な認証リンクです。');
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null, // 認証完了後、トークンを削除
        ]);

        // ✅ `update()` の代わりに `save()` を使用
        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $success = $user->save();
        //TODO:新しくタスクが追加された、ログインページに行くのではなく専用のページにいくように
        return redirect()->route('login')->with('message', 'メール認証が完了しました！');
    }

    /**
     * 認証メール再送信
     */
    public function resendVerificationEmail()
    {
        $user = Auth::user();

        if ($user->email_verified_at) {
            return redirect()->route('home')->with('message', 'すでに認証済みです。');
        }

        Mail::send('emails.verify', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('メール認証のお願い（再送）');
        });

        return back()->with('message', '確認メールを再送しました！');
    }

    /**
     * ログイン処理（未認証ユーザーはログイン不可）
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->email_verified_at) {
            return back()->withErrors([
                'email' => 'メールアドレスが認証されていません。',
            ]);
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // 初回ログイン時のみ `profile.blade.php` へリダイレクト
            if (!$user->profile_completed) {
                return redirect()->route('profile.edit'); 
            }
            //通常のログイン時
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が正しくありません。',
        ])->onlyInput('email');
    }

    /**
     * ログアウト処理
     */    
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'ログアウトしました。');
    }
}
