<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Good;
use App\Models\User;
use App\Models\Comment;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;


class ItemController extends Controller
{
    public function index()
    {
        $goods = Good::all(); // 全商品の取得

        return view('index', compact('goods'));
    }

    public function show($id)
    {
        $good = Good::with('comments.user')->findOrFail($id); // コメントとユーザー情報を一緒に取得
        $comments = $good->comments()->latest()->get(); // コメントを取得

        return view('goods.goods-detail', compact('good','comments'));
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
    public function toggle($goodId)
    {
        $user = Auth::user();
        $good = Good::findOrFail($goodId);

        if ($good->isFavoritedBy($user)) {
            Favorite::where('user_id', $user->id)->where('good_id', $goodId)->delete();
            return response()->json(['status' => 'unfavorited', 'count' => $good->favorites()->count()]);
        } else {
            Favorite::create(['user_id' => $user->id, 'good_id' => $goodId]);
            return response()->json(['status' => 'favorited', 'count' => $good->favorites()->count()]);
        }
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
