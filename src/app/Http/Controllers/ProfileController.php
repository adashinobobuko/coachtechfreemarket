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
use App\Models\Transaction;
use App\Models\TransactionMessage;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // 評価の平均値を取得するヘルパー関数
    private function getAverageRating($userId)
    {
        return Evaluation::where('to_user_id', $userId)->avg('rating');
    }

    // プロフィール設定画面を表示
    public function edit()
    {
        //登録ユーザーの情報を取得
        $user = Auth::user();

        return view('mypage/profile', compact('user'));
    }

    // プロフィール画像を更新
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
        $user = Auth::user();
        $goods = Good::where('user_id', Auth::id())->get(); // ログインユーザーの商品取得
        $activeTab = 'sell';// デフォルトの値を定義
        $favorites = Auth::check() ? Favorite::where('user_id', Auth::id())->with('good')->get() : collect();
        $averageRating = $this->getAverageRating($user->id);

        return view('mypage.mypage', [
            'goods' => $goods,
            'favorites' => $favorites,
            'activeTab' => 'sell',
            'averageRating' => $averageRating,
            'transactions' => collect(), 
        ]);
    }

    // 「購入した商品」タブ（購入した商品ページ）
    public function buy()
    {
        $user = Auth::user();
        $purchases = Purchase::where('user_id', auth()->id())->with('good')->get();
        $activeTab = 'buy';// 購入した商品リストをアクティブにする
        $favorites = Auth::check() ? Favorite::where('user_id', Auth::id())->with('purchase')->get() : collect();
        $averageRating = $this->getAverageRating($user->id);

        return view('mypage.mypage', [
            'purchases' => $purchases,
            'favorites' => $favorites,
            'activeTab' => 'buy',
            'averageRating' => $averageRating,
            'transactions' => collect(), 
        ]);
    }

    public function transactions()
    {
        $user = Auth::user();
    
        // 自分が関わる取引のID（購入者か出品者）を取得
        $transactionIds = Transaction::where(function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                      ->orWhere('seller_id', $user->id);
            })
            ->whereIn('status', ['in_progress', 'completed']) // 進行中 or 完了済み
            ->pluck('id');
    
        // 新着メッセージ数（相手からで、未読）をカウント
        $unreadCounts = TransactionMessage::selectRaw('transaction_id, COUNT(*) as count')
            ->whereIn('transaction_id', $transactionIds)
            ->where('recipient_id', $user->id)
            ->where('is_read', false)
            ->groupBy('transaction_id')
            ->pluck('count', 'transaction_id');
    
        // 完了済みで評価が未完了の取引だけを取得
        $transactions = Transaction::whereIn('id', $transactionIds)
            ->with(['good', 'buyer', 'seller', 'purchase'])
            ->orderBy('updated_at', 'desc') // または created_at
            ->get()
            ->map(function ($transaction) use ($unreadCounts) {
                $transaction->unread_count = $unreadCounts[$transaction->id] ?? 0;
                return $transaction;
            });    
    
        $favorites = Favorite::where('user_id', $user->id)->with('good')->get();
    
        // 評価の平均を取得
        $averageRating = $this->getAverageRating($user->id);
    
        return view('mypage.mypage', [
            'transactions' => $transactions,
            'favorites' => $favorites,
            'averageRating' => $averageRating,
            'activeTab' => 'transactions',  // 'transactions'
        ]);
    }

}
