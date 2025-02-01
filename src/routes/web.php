<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MarketController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//トップページへ
Route::get('/', [MarketController::class,'index']);

//ユーザー登録のルート
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

//ログイン関連のルート
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); // ログインフォーム表示
Route::post('/login', [AuthController::class, 'login'])->name('login.submit'); // ログイン処理
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//プロフィール編集画面へのルート
Route::middleware(['auth'])->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

//マイページ関連のルート
Route::middleware(['auth'])->group(function () {
    Route::get('/mypage',[ProfileController::class,'showMypage'])->name('mypage');
});

//メール認証必要なルートのちに編集予定
Route::middleware(['auth', 'verified'])->group(function () {
    // 認証後、メール確認が必要なルートを記述
});
