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
                <!-- いいねボタン -->
                <button id="favorite-btn" class="btn btn-outline-warning me-3">
                    <img src="{{ asset('images/1fc8ae66e54e525cb4afafb0a04b1deb.png') }}" alt="お気に入り" width="20">
                    <span id="favorite-count">0</span>
                </button>

                <!-- コメント数表示 -->
                <span>
                    <img src="{{ asset('images/9403a7440cf0d1765014bcdbe8540f70.png') }}" alt="コメント" width="20">
                    <span id="comment-count">{{ isset($comments) ? $comments->count() : 0 }}</span>
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

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('favorite-btn').addEventListener('click', function() {
        let goodId = this.dataset.goodId;
        let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/favorites/${goodId}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": token,
                "Content-Type": "application/json"
            },
        })
        .then(response => response.json())
        .then(data => {
            let favoriteBtn = document.getElementById('favorite-btn');
            let favoriteCount = document.getElementById('favorite-count');

            favoriteCount.textContent = data.count;
            favoriteBtn.classList.toggle('btn-outline-warning');
            favoriteBtn.classList.toggle('btn-warning');
        })
        .catch(error => console.error('Error:', error));
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const favoriteBtn = document.getElementById("favorite-btn");
    const favoriteCount = document.getElementById("favorite-count");
    const commentBtn = document.getElementById("add-comment-btn");
    const commentCount = document.getElementById("comment-count");
    const commentInput = document.getElementById("comment-input");

    // いいねボタンの動作
    let liked = false;
    favoriteBtn.addEventListener("click", function () {
        let count = parseInt(favoriteCount.textContent);
        if (liked) {
            count--;
            liked = false;
        } else {
            count++;
            liked = true;
        }
        favoriteCount.textContent = count;
    });

    // コメント追加の動作
    commentBtn.addEventListener("click", function () {
        if (commentInput.value.trim() !== "") {
            let count = parseInt(commentCount.textContent);
            count++;
            commentCount.textContent = count;
            commentInput.value = ""; // 入力欄をクリア
        }
    });
});
</script>

