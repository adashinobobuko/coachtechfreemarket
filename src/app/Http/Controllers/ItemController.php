<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Good;
use App\Models\User;
use App\Models\Comment;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class ItemController extends Controller
{
    // 「おすすめ」タブ（デフォルトのトップページ）
    public function recommend()
    {
        $goods = Good::where('user_id', '!=', Auth::id())->get();// 他人が出品した商品のみ取得
        $activeTab = 'recommend';// デフォルトの値を定義
        $favorites = Auth::check() ? Favorite::where('user_id', Auth::id())->with('good')->get() : collect();

        return view('index', [
            'goods' => $goods,
            'favorites' => $favorites,
            'activeTab' => 'recommend'
        ]);
    }

    // 「マイリスト」タブ（/mylist ページ）
    public function mylist()
    {
        $goods = Good::all(); // 全商品の取得
        $activeTab = 'mylist';// マイリストをアクティブにする
        $favorites = Auth::check() ? Favorite::where('user_id', Auth::id())->with('good')->get() : collect();

        return view('index', [
            'goods' => $goods,
            'favorites' => $favorites,
            'activeTab' => 'mylist'
        ]);
    }

    public function show($id)
    {
        // 商品情報を取得し、いいね（favorites）とコメントを一緒に取得
        $good = Good::with('favorites', 'comments.user')->findOrFail($id);

        // 商品に紐づく全てのコメントを取得（新しい順）
        $comments = Comment::where('good_id', $good->id)->latest()->get();

        // いいね数を取得
        
        return view('goods.goods-detail', compact('good', 'comments'));
    }

    public function store(CommentRequest $request, Good $good)
    {
        if (!Auth::check()) {
            return redirect()->route('goods.show', $good->id)->with('error', 'コメントを投稿するにはログインが必要です。');
        }

        // バリデーション
        $validated = $request->validated();

        // コメントを保存
        $comment = new Comment();
        $comment->user_id = Auth::id();
        $comment->good_id = $good->id;
        $comment->content = $validated['content']; // 修正

        $comment->save();

        return redirect()->route('goods.show', $good->id)->with('success', 'コメントを投稿しました！');
    }

    //いいね
    public function toggle(Request $request)
    {
        $request->validate([
            'good_id' => 'required|exists:goods,id',
        ]);

        Favorite::create([
            'user_id' => auth()->id(),
            'good_id' => $request->good_id,
        ]);

        return back()->with('success', 'いいねしました！');
    }

    public function destroy(Request $request,$id)
    {
        $like = Favorite::where('user_id', auth()->id())->where('good_id', $id)->first();
        if ($like) {
        $like->delete();
        }

        return back()->with('success', 'いいねを解除しました！');
    }
    

    //検索
    public function search(Request $request)
    {
        $query = Good::query();

        if (!empty($request->keyword)) {
            $query->keywordSearch($request->keyword);
        }

        // どのタブから検索されたのかを判定
        $activeTab = $request->input('tab', 'recommend'); // デフォルトは「おすすめ」

        // 「マイリスト」タブで検索する場合は、ログインユーザーのお気に入りのみを取得
        if ($activeTab === 'mylist' && Auth::check()) {
            $query->whereHas('favorites', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        $goods = $query->get();
        $favorites = Auth::check() ? Favorite::where('user_id', Auth::id())->with('good')->get() : collect();

        return view('index', [
            'goods' => $goods,
            'favorites' => $favorites,
            'activeTab' => $activeTab // 検索時のタブ状態を保持
        ]);
    }
}