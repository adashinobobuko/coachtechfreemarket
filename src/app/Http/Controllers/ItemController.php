<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Good;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;


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

        return view('goods.goods-detail', compact('good'));
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
}
