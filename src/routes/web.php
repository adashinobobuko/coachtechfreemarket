<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\BuyController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Response;

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
// デフォルト（トップページ）は「おすすめ」
Route::get('/', [ItemController::class, 'recommend'])->name('index');
// マイリストページ
Route::get('/mylist', [ItemController::class, 'mylist'])->name('mylist');
Route::get('/search', [ItemController::class, 'search'])->name('search');

//商品詳細閲覧、購入のルート
Route::get('/item/{id}', [ItemController::class, 'show'])->name('goods.show');//商品の詳細を表示

//マイページ関連のルート
Route::middleware(['auth'])->group(function () {
    Route::get('/mypage/buy',[ProfileController::class,'buy'])->name('mypage.buy');
    Route::get('/mypage/sell',[ProfileController::class,'sell'])->name('mypage.sell');
});

//購入のルート
Route::middleware(['auth'])->group(function(){
    Route::get('/purchase/{id}', [BuyController::class, 'showBuyform'])->name('buy.show');
    Route::post('/purchase/store', [PurchaseController::class, 'store'])->name('purchase.store');
});

//住所変更ルート
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/address/{goodsid}', [BuyController::class, 'showForm'])
        ->name('address.change.form'); 
    Route::post('/goods/address-change', [BuyController::class, 'updateAddress'])
        ->name('address.change.update');
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

//プロフィール編集画面へのルート
Route::middleware(['auth'])->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile/update', [ProfileController::class, 'update'])->name('profile.update');
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
Route::get('/verify-form', [AuthController::class, 'showVerifyForm'])->name('verify.form');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/resend-email', [AuthController::class, 'resendVerificationEmail'])->name('resend.email');
