@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- 商品画像 -->
        <div class="col-md-6 text-center">
            <img src="{{ asset('storage/' . $good->image) }}" alt="商品画像" class="img-fluid border">
        </div>

        <!-- 商品詳細 -->
        <div class="col-md-6">
            <h2>{{ $good->name }}</h2>
            <p class="text-muted">{{ $good->brand ?? 'ブランド名' }}</p>
            <h3 class="text-danger">¥{{ number_format($good->price) }}（税込）</h3>

            <!-- お気に入り＆コメントアイコン -->
            <div class="d-flex align-items-center mb-3">
                <!-- いいねボタン（フォーム利用） -->
                    @if(Auth::check() && optional(Auth::user()->favorites)->contains('good_id', $good->id))
                        <form action="{{ route('like.destroy', ['id' => $good->id]) }}" method="POST">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-warning">
                                <img src="{{ asset('images/1fc8ae66e54e525cb4afafb0a04b1deb.png') }}" alt="お気に入り解除" width="20">
                                いいね解除
                            </button>
                        </form>
                    @else
                        <form action="{{ route('like.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="good_id" value="{{ $good->id }}">
                            <button type="submit" class="btn btn-outline-warning">
                                <img src="{{ asset('images/1fc8ae66e54e525cb4afafb0a04b1deb.png') }}" alt="お気に入り" width="20">
                                いいね
                            </button>
                        </form>
                    @endif
                <!-- いいね数を表示 -->
                <span class="ms-2">{{ $good->favorites->count() }}</span>

                <!-- コメント数表示 -->
                <span>
                    <img src="{{ asset('images/9403a7440cf0d1765014bcdbe8540f70.png') }}" alt="コメント" width="20">
                    <span>{{ isset($comments) ? $comments->count() : 0 }}</span>
                </span>
            </div>
            <!-- 購入ボタン -->
            <a href="{{ route('buy.show', ['id' => $good->id]) }}" class="btn btn-danger w-100">購入手続きへ</a>

            <!-- 商品説明 -->
            <div class="mt-4">
                <h4>商品説明</h4>
                <p>{{ $good->description ?? '商品説明がありません。' }}</p>
            </div>

            <!-- 商品の情報 -->
            <div class="mt-3">
                <h4>商品の情報</h4>
                <span class="badge bg-secondary">{{ $good->category ?? 'カテゴリ不明' }}</span>
                <p><strong>状態：</strong>{{ $good->condition ?? '不明' }}</p>
            </div>
        </div>
    </div>

    <!-- コメントセクション -->
    <div class="mt-5">
        <h4>コメント ({{ isset($comments) ? $comments->count() : 0 }})</h4>

            @if($comments->isNotEmpty())
                @foreach ($comments as $comment)
                    <div class="d-flex align-items-center border p-2 mb-2">
                        <img src="{{ asset('storage/' . ($comment->user->profile_image ?? 'default.png')) }}" 
                            alt="ユーザー画像" class="rounded-circle" style="width: 40px; height: 40px;">
                        <div class="ms-3">
                            <strong>{{ $comment->user->name ?? '匿名ユーザー' }}</strong>
                            <p class="mb-0">{{ $comment->content }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                <p>まだコメントはありません。</p>
            @endif


        <!-- 成功・エラーメッセージの表示 -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- コメント投稿フォーム -->
        <div class="mt-3">
            <h4>商品へのコメント</h4>
                <form action="{{ route('comments.store', ['good' => $good->id]) }}" method="POST">
                    @csrf
                    <textarea name="content" class="form-control" rows="3" placeholder="コメントを入力してください">{{ old('content') }}</textarea>

                    @if ($errors->has('content'))
                        <div class="text-danger">{{ $errors->first('content') }}</div>
                    @endif

                    <button type="submit" class="btn btn-danger mt-2 w-100">コメントを送信する</button>
                </form>
        </div>
    </div>
</div>
@endsection
