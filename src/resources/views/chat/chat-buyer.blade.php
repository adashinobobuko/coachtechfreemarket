@extends("layouts.app")

@section("css")
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section("content")
    <div class="transaction-chat">
        <h2 class="chat-title">{{ $otherUser->name }} さんとの取引画面</h2>
        <form id="complete-form" action="{{ route('transactions.complete', ['purchase' => $purchase->id]) }}" method="POST">
            @csrf
            <button type="submit" id="complete-button">取引を完了する</button>
        </form>

        <div class="sidebar">
        @foreach ($otherTransactions as $t)
            <a href="{{ route('chat.buyer', $t->id) }}"
            class="sidebar-item {{ $t->id === $purchase->id ? 'active' : '' }}">
                <div class="sidebar-title">{{ $t->good->name }}</div>
            </a>
        @endforeach
        </div>

        @if ($transaction->status === 'completed')
            <p>取引完了</p>
        @endif

        @php
            $transaction = $purchase->transaction; // リレーションがある前提
        @endphp

        {{-- 評価モーダル --}}
        @if(request('completed'))
        <div id="complete-modal">
            <form method="POST" action="{{ route('evaluations.store', $purchase->id) }}">
                @csrf
                <textarea name="comment" required></textarea>
                <select name="rating" required>
                    <option value="5">★★★★★</option>
                    <option value="4">★★★★☆</option>
                    <option value="3">★★★☆☆</option>
                    <option value="2">★★☆☆☆</option>
                    <option value="1">★☆☆☆☆</option>
                </select>
                <button type="submit">評価を送信</button>
            </form>
            <button id="modal-close">閉じる</button>
        </div>
        @endif

        <div class="product-info">
            <div class="product-image">
            <img src="{{ asset('storage/' . $good->image) }}" alt="商品画像" width="100">
            </div>
            <div class="product-details">
            <p class="product-name">{{ $good->name }}</p>
            <p class="product-price">{{ $good->price }}円</p>
            </div>
        </div>

        <div class="message-list">
        @foreach($messages as $message)
        <div class="message-item {{ $message->user_id === Auth::id() ? 'my-message' : 'other-message' }}" data-id="{{ $message->id }}">
            <p class="message-user">{{ $message->user->name }}</p>

            @if($message->user_id === Auth::id())
                {{-- 表示用吹き出し（クリックでtextarea表示） --}}
                <div class="message-content js-click-to-edit" data-id="{{ $message->id }}">
                    {{ $message->message }}
                </div>

                @if ($message->image_path)
                <img src="{{ asset('storage/' . $message->image_path) }}" class="mt-2 max-w-xs rounded">
                @endif

                {{-- 編集フォーム（最初は非表示） --}}
                <form action="{{ route('chat.update', $message->id) }}" method="POST" class="js-edit-form" data-id="{{ $message->id }}" style="display: none;">
                    @csrf
                    @method('PUT')
                    <textarea name="message" class="edit-textarea" rows="2">{{ $message->message }}</textarea>
                </form>

                <div class="message-actions">
                    {{-- 編集ボタンがsubmitをトリガー --}}
                    <button type="button" class="js-edit-submit" data-id="{{ $message->id }}">編集</button>

                    <form action="{{ route('chat.delete', $message->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">削除</button>
                    </form>
                </div>
            @else
                <div class="message-content">{{ $message->message }}</div>
            @endif
        </div>
        @endforeach
        </div>

        @if($errors->any())
            <div class="error-messages">
                @foreach ($errors->all() as $error)
                    <p class="error">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const completeForm = document.getElementById('complete-form');
            const completeModal = document.getElementById('complete-modal');

            // formのsubmit完了後（ページ遷移後）にモーダル表示したい場合はサーバ側でフラッシュなど使う
            // ここではフロントだけで再現例として↓
            if (window.location.search.includes('completed=1')) {
                completeModal?.classList.remove('hidden');
            }

            // モーダル閉じる処理
            const modalClose = document.getElementById('modal-close');
            modalClose?.addEventListener('click', () => {
                completeModal?.classList.add('hidden');
            });
        });

        // 吹き出しクリック → textarea 表示
        document.querySelectorAll('.js-click-to-edit').forEach(elem => {
            elem.addEventListener('click', () => {
                const id = elem.dataset.id;
                elem.style.display = 'none';
                document.querySelector(`.js-edit-form[data-id="${id}"]`).style.display = 'block';
            });
        });

        // 編集ボタン → 該当フォームをsubmit
        document.querySelectorAll('.js-edit-submit').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;
                document.querySelector(`.js-edit-form[data-id="${id}"]`).submit();
            });
        });

        // ローカルストレージでチャット内容を保持
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

        window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            // 戻るボタンで戻ってきたときだけリロード
            window.location.reload();
        }
    });
    </script>

@endsection