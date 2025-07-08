@extends("layouts.app")

@section("css")
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section("content")
<div class="transaction-chat">
    <h2 class="chat-title">{{ $otherUser->name }} さんとの取引画面</h2>

    <div class="sidebar">
    @foreach ($otherTransactions as $t)
        <a href="{{ route('chat.seller', $t->id) }}"
           class="sidebar-item {{ $t->id === $purchase->id ? 'active' : '' }}">
            <div class="sidebar-title">{{ $t->good->name }}</div>
        </a>
    @endforeach
    </div>

    {{-- 取引完了モーダル自動表示スクリプト --}}
    @if($transaction->status === 'completed' && !$alreadyEvaluated)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('complete-modal');
            if (modal) modal.classList.remove('hidden');
        });
    </script>
    @endif

    @if ($transaction->status === 'completed')
        <p>取引完了</p>
    @endif

    @php
        $transaction = $purchase->transaction; // リレーションがある前提
    @endphp

    {{-- 評価モーダル --}}
    @if ($transaction && $transaction->status === 'completed' && !$alreadyEvaluated)
    <div id="complete-modal" class="hidden">
        <form action="{{ route('evaluations.store', $purchase->id) }}" method="POST">
            @csrf
            <label for="rating">評価（1〜5）</label>
            <select name="rating" required>
                <option value="5">★5</option>
                <option value="4">★4</option>
                <option value="3">★3</option>
                <option value="2">★2</option>
                <option value="1">★1</option>
            </select>

            <label for="comment">コメント</label>
            <textarea name="comment" rows="3"></textarea>

            <button type="submit">評価を送信</button>
        </form>
    </div>
    @endif

    {{-- 商品情報 --}}
    <div class="product-info">
        <div class="product-image">
            <img src="{{ asset('storage/' . $good->image) }}" alt="商品画像" width="100">
        </div>
        <div class="product-details">
            <p class="product-name">{{ $good->name }}</p>
            <p class="product-price">{{ $good->price }}円</p>
        </div>
    </div>

    {{-- チャットメッセージ一覧 --}}
    <div class="message-list">
        @foreach($messages as $message)
        <div class="message-item {{ $message->user_id === Auth::id() ? 'my-message' : 'other-message' }}" data-id="{{ $message->id }}">
            <p class="message-user">{{ $message->user->name }}</p>

            @if($message->user_id === Auth::id())
                {{-- 自分のメッセージ：編集可 --}}
                <div class="message-content js-click-to-edit" data-id="{{ $message->id }}">
                    {{ $message->message }}
                </div>

                @if ($message->image_path)
                <img src="{{ asset('storage/' . $message->image_path) }}" class="mt-2 max-w-xs rounded">
                @endif

                <form action="{{ route('chat.update', $message->id) }}" method="POST" class="js-edit-form" data-id="{{ $message->id }}" style="display: none;">
                    @csrf
                    @method('PUT')
                    <textarea name="message" class="edit-textarea" rows="2">{{ $message->message }}</textarea>
                </form>

                <div class="message-actions">
                    <button type="button" class="js-edit-submit" data-id="{{ $message->id }}">編集</button>
                    <form action="{{ route('chat.delete', $message->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">削除</button>
                    </form>
                </div>
            @else
                {{-- 相手のメッセージ --}}
                <div class="message-content">{{ $message->message }}</div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- バリデーションエラー表示 --}}
    @if($errors->any())
        <div class="error-messages">
            @foreach ($errors->all() as $error)
                <p class="error">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- 成功メッセージ --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- チャット投稿フォーム --}}
    <form action="{{ route('chat.send', $purchase->id) }}" method="POST">
        @csrf
        <textarea name="message" id="message-body" rows="3" placeholder="取引メッセージを入力してください" class="message-input"></textarea>

        <input type="file" name="image" id="image" class="hidden">
        <label for="image" class="bg-gray-200 text-sm px-3 py-2 rounded cursor-pointer">
                画像追加
        </label>

        <button type="submit" class="message-submit">送信</button>
    </form>
</div>

{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 吹き出しクリック → textarea 表示
    document.querySelectorAll('.js-click-to-edit').forEach(elem => {
        elem.addEventListener('click', () => {
            const id = elem.dataset.id;
            elem.style.display = 'none';
            document.querySelector(`.js-edit-form[data-id="${id}"]`).style.display = 'block';
        });
    });

    // 編集ボタン → 該当フォームsubmit
    document.querySelectorAll('.js-edit-submit').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            document.querySelector(`.js-edit-form[data-id="${id}"]`).submit();
        });
    });

    // ローカルストレージ保持
    const textarea = document.getElementById('message-body');
    const key = location.pathname + '-draft';

    textarea?.addEventListener('input', () => {
        localStorage.setItem(key, textarea.value);
    });

    const saved = localStorage.getItem(key);
    if (saved && textarea) textarea.value = saved;

    const form = document.querySelector('form');
    form?.addEventListener('submit', () => {
        localStorage.removeItem(key);
    });
    });

    window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // 戻るボタンで戻ってきたときだけリロード
                window.location.reload();
            }
        });
</script>
@endsection
