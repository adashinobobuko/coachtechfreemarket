<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    // 登録フォームの表示
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // ログインフォームの表示
    public function showLoginForm()
    {
        return view('auth.login');
    }

    //ユーザー登録処理
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verification_token' => Str::random(64),
        ]);

        // 認証メール送信
        Mail::send('emails.verify', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('メール認証のお願い');
        });

        // ✅ `session()->put()` を使ってユーザー情報を保存
        session()->put('user_id', $user->id);

        // ✅ `verify.form` にリダイレクト
        return redirect()->route('verify.form')->with([
            'email' => $user->email,
            'message' => '認証メールを送信しました！メールを確認してください。'
        ]);
    }

    //メール認証待機画面の表示
    public function showVerifyForm()
    {
        // セッションからユーザーを取得
        $user = User::find(session('user_id'));

        if (!$user) {
            return redirect()->route('register.form')->with('error', '登録情報が見つかりませんでした。');
        }

        return view('auth.verifyform', compact('user'));
    }

    //メール認証の処理
    public function verifyEmail($token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', '無効な認証リンクです。');
        }

        // 認証を完了させる
        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->save();

        // 自動ログイン
        Auth::login($user);

        // プロフィール編集ページへリダイレクト
        return redirect()->route('profile.edit')->with('message', 'メール認証が完了しました！プロフィールを編集してください。');
    }

    //認証メール再送信
    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return redirect()->route('login')->with('message', 'すでに認証済みです。');
        }

        // 新しいトークンを発行
        $user->email_verification_token = Str::random(64);
        $user->save();

        // 認証メール再送
        Mail::send('emails.verify', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('メール認証のお願い（再送）');
        });

        return back()->with('message', '確認メールを再送しました！');
    }


    //ログイン処理
    public function login(LoginRequest $request)
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

    //ログアウト処理
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'ログアウトしました。');
    }
}
