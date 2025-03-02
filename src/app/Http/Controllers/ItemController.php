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
        \Log::info('検索リクエスト開始', [
            'keyword' => $request->keyword,
            'tab' => $request->input('tab'),
        ]);

        $query = Good::query();

        // `tab` の値を取得（デフォルトは「recommend」）
        $activeTab = $request->input('tab', 'recommend');

        // 検索時に「おすすめ」タブを開いたら、自分の商品を除外
        if ($activeTab === 'recommend' && Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

        // 検索が実行された場合は `recommend` に変更するが、タブが `mylist` の場合は変更しない
        if ($request->has('keyword') && $activeTab !== 'mylist') {
            $activeTab = 'recommend';
        }

        \Log::info('最終的なタブの値', ['activeTab' => $activeTab]);

        // ユーザーのお気に入り商品のIDを取得
        $favoriteIds = Favorite::where('user_id', Auth::id())->pluck('good_id')->toArray();

        // マイリストを表示する場合は `whereIn()` を適用
        if ($activeTab === 'mylist' && Auth::check()) {
            if (!empty($favoriteIds)) {
                $query->whereIn('id', $favoriteIds);
                \Log::info('マイリストの検索適用', ['favoriteIds' => $favoriteIds]);
            } else {
                return view('index', [
                    'goods' => collect(), // マイリストが空なら結果も空にする
                    'favorites' => ($activeTab === 'mylist' && !$request->has('keyword')) 
                    ? Favorite::where('user_id', Auth::id())->with('good')->get() 
                    : collect(), // 検索時はマイリストの商品を取得しない
                    'activeTab' => 'mylist',
                    'keyword' => $request->keyword,
                ]);
            }
        }

        // キーワード検索を適用
        if (!empty($request->keyword)) {
            $query->keywordSearch($request->keyword);
            \Log::info('キーワード検索を適用', ['keyword' => $request->keyword]);
        }

        // 検索結果を取得
        $goods = $query->get();
        \Log::info('検索結果取得', ['goods_count' => count($goods)]);

        return view('index', [
            'goods' => $goods,
            'favorites' => Auth::check() ? Favorite::where('user_id', Auth::id())->with('good')->get() : collect(),
            'activeTab' => $activeTab,
            'keyword' => $request->keyword,
        ]);
    }

}