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
    public function index()
    {
        $goods = Good::all(); // 全商品の取得
        $favorites = Auth::check() ? Favorite::where('user_id', Auth::id())->with('good')->get() : collect();

        // dd([
        // 'favorites_count' => $favorites->count(),
        // 'favorites_data' => $favorites,
        // ]);

        return view('index', compact('goods','favorites'));
    }

    public function show($id)
    {
        // 商品情報を取得し、いいね（favorites）とコメントを一緒に取得
        $good = Good::with('favorites', 'comments.user')->findOrFail($id);

        //dd($good->favorites->count());

        // 商品に紐づく全てのコメントを取得（新しい順）
        $comments = $good->comments()->latest()->get();

        // いいね数を取得
        
        return view('goods.goods-detail', compact('good', 'comments'));
    }

    public function store(CommentRequest $request, Good $good)
    {
        // バリデーション
        $validated = $request->validated();

        // コメントを保存
        $comment = new Comment();
        $comment->user_id = Auth::id();
        $comment->good_id = $good->id;
        $comment->content = $request->input('content');
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

        $goods = $query->get();

        return view('index', compact('goods'));
    }

}
