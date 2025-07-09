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
use Illuminate\Support\Facades\DB;

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

    // ProfileController 内に追加（または適切な基底Controllerに）
    private function getUnreadTransactionCount($userId)
    {
        $transactionIds = Transaction::where('buyer_id', $userId)
            ->orWhere('seller_id', $userId)
            ->pluck('id');

        return TransactionMessage::whereIn('transaction_id', $transactionIds)
            ->where('recipient_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    // 「出品した商品」タブ（出品した商品マイページ）
    public function sell()
    {
        $user = Auth::user();
        $goods = Good::where('user_id', Auth::id())->get(); // ログインユーザーの商品取得
        $activeTab = 'sell';// デフォルトの値を定義
        $favorites = Auth::check() ? Favorite::where('user_id', Auth::id())->with('good')->get() : collect();
        $averageRating = $this->getAverageRating($user->id);

        $averageRating = $this->getAverageRating($user->id);
        $unreadCount = $this->getUnreadTransactionCount($user->id);
        
        return view('mypage.mypage', [
            'goods' => $goods,
            'favorites' => $favorites,
            'activeTab' => 'sell',
            'averageRating' => $averageRating,
            'transactions' => collect(),
            'unreadCount' => $unreadCount,
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

        $averageRating = $this->getAverageRating($user->id);
        $unreadCount = $this->getUnreadTransactionCount($user->id);
        
        return view('mypage.mypage', [
            'purchases' => $purchases,
            'favorites' => $favorites,
            'activeTab' => 'buy',
            'averageRating' => $averageRating,
            'transactions' => collect(),
            'unreadCount' => $unreadCount,
        ]);
    }

    public function transactions()
    {
        $user = Auth::user();
    
        // 自分の関係する取引ID
        $transactionIds = Transaction::where(function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                      ->orWhere('seller_id', $user->id);
            })
            ->whereIn('status', ['in_progress', 'completed'])
            ->pluck('id');
    
        // 各取引の最新メッセージの日時を取得
        $latestMessages = TransactionMessage::select('transaction_id', DB::raw('MAX(created_at) as last_message_at'))
            ->whereIn('transaction_id', $transactionIds)
            ->groupBy('transaction_id');
    
        // 最新メッセージがある順に取引を並べる（評価済みのものは除外）
        $transactions = Transaction::whereIn('id', $transactionIds)
            ->whereDoesntHave('purchase.evaluations', function ($query) {
                // 評価が2件（購入者・出品者）揃っていたら除外したい
                $query->select('transaction_id')
                    ->groupBy('transaction_id')
                    ->havingRaw('COUNT(*) >= 2');
            })
            ->leftJoinSub($latestMessages, 'latest_messages', function ($join) {
                $join->on('transactions.id', '=', 'latest_messages.transaction_id');
            })
            ->with(['good', 'buyer', 'seller', 'purchase.evaluations']) // 評価も読み込んでおく
            ->orderByDesc('latest_messages.last_message_at')
            ->orderByDesc('transactions.updated_at')
            ->get();
    
        // 各取引の未読数を追加
        $unreadCounts = TransactionMessage::selectRaw('transaction_id, COUNT(*) as count')
            ->whereIn('transaction_id', $transactionIds)
            ->where('recipient_id', $user->id)
            ->where('is_read', false)
            ->groupBy('transaction_id')
            ->pluck('count', 'transaction_id');
    
        $transactions->each(function ($transaction) use ($unreadCounts) {
            $transaction->unread_count = $unreadCounts[$transaction->id] ?? 0;
        });
    
        $favorites = Favorite::where('user_id', $user->id)->with('good')->get();
        $averageRating = $this->getAverageRating($user->id);
        $unreadCount = $unreadCounts->sum();
    
        return view('mypage.mypage', [
            'transactions' => $transactions,
            'favorites' => $favorites,
            'averageRating' => $averageRating,
            'activeTab' => 'transactions',
            'unreadCount' => $unreadCount,
        ]);
    }
}
