<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\BuyController;
use App\Http\Controllers\PurchaseController;

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

//トップ（商品一覧）ページへ
Route::get('/', [ItemController::class,'index'])->name('index');

//商品詳細閲覧、購入のルート
Route::get('/item/{id}', [ItemController::class, 'show'])->name('goods.show');//商品の詳細を表示

//購入のルート
Route::middleware(['auth'])->group(function(){
    Route::get('/purchase/{id}',[BuyController::class,'showBuyform'])->name('buy.show');
    Route::post('/purchase/store', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::post('/purchase/complete', [PurchaseController::class, 'complete'])->name('purchase.complete');
});

//住所変更ルート
Route::middleware(['auth'])->group(function () {
    Route::get('/goods/address-change', [BuyController::class, 'showForm'])->name('address.change.form');
    Route::post('/goods/address-change', [BuyController::class, 'updateAddress'])->name('address.change.update');
    Route::get('/purchase', [BuyController::class, 'showAll'])->name('buy.index');
});

//いいね、マイリスト機能関連のルート、コメントするためのルート
Route::middleware(['auth'])->group(function () {
    Route::post('/like', [ItemController::class, 'toggle'])->name('like.store');
    Route::post('/unlike/{id}', [ItemController::class, 'destroy'])->name('like.destroy');    
});
Route::post('/comments/{good}', [ItemController::class, 'store'])->name('comments.store');

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

//出品関連のルート
Route::middleware(['auth'])->group(function () {
    Route::get('/sell',[ListingController::class,'showSellForm'])->name('sellform');
    Route::post('/sell/store',[ListingController::class,'store'])->name('sellform.store');
});

//商品を検索するためのルート
Route::get('/search',[ItemController::class,'search'])->name('search');

//メール認証関連
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify.email');
Route::post('/resend-email', [AuthController::class, 'resendVerificationEmail'])->middleware('auth')->name('resend.email');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
