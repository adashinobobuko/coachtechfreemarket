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
        if (!Auth::check()) {
        // 未認証ユーザーは空の商品リストを返す
        return view('index', [
            'goods' => collect(), 
            'favorites' => collect(),
            'activeTab' => 'mylist'
        ]);
        }

        // ユーザーのお気に入り商品のIDを取得
        $favoriteIds = Favorite::where('user_id', Auth::id())->pluck('good_id')->toArray();

        $favorites = Auth::check() ? Favorite::where('user_id', Auth::id())->with('good')->get() : collect();
        $goods =!empty($favoriteIds) ? Good::whereIn('id', $favoriteIds)->get() : collect();//マイリストに入れたもののみを表示
        $activeTab = 'mylist';// マイリストをアクティブにする

        return view('index', [
            'goods' => $goods,
            'favorites' => $favorites,
            'activeTab' => 'mylist'
        ]);
    }

    //詳細ページ
    public function show($id)
    {
        // 商品情報を取得し、いいね（favorites）とコメントを一緒に取得
        $good = Good::with('favorites', 'comments.user')->findOrFail($id);

        // 商品に紐づく全てのコメントを取得（新しい順）
        $comments = Comment::where('good_id', $good->id)->latest()->get();

        // いいね数を取得  
        return view('goods.goods-detail', compact('good', 'comments'));
    }

    //コメント機能
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
        $activeTab = $request->tab ?? 'recommend';
        $query = Good::query();

        // おすすめタブの場合は自分の出品した商品を除外
        if ($activeTab === 'recommend') {
            $query->where('user_id', '!=', Auth::id());
        }

        // マイリスト検索機能
        if (Auth::check() && $activeTab === 'mylist') {
            $favoriteIds = Favorite::where('user_id', auth()->id())->pluck('good_id')->toArray(); // いいねした商品IDを取得

            $query->when(!empty($favoriteIds), function ($query) use ($favoriteIds) {
                return $query->whereIn('id', $favoriteIds);
            }, function ($query) {
                return $query->whereRaw('1 = 0'); // いいねがない場合、結果を空にする
            });
        }

        // キーワード検索を適用
        $query->when($request->filled('keyword'), function ($query) use ($request) {
            return $query->KeywordSearch($request->keyword);
        });

        // 検索結果取得
        $goods = $query->get();

        return view('index', compact('goods', 'activeTab'));
    }

}