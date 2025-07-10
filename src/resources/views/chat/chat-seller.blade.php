@extends("layouts.app")

@section("css")
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section("content")
    <div class="transaction-chat">
        <div class="chat-wrapper">

            {{-- サイドバー --}}
            <div class="sidebar">
                <h2 class="sidebar-title">その他の取引</h2>
                @foreach ($otherTransactions as $t)
                    <a href="{{ route('chat.seller', $t->id) }}"
                       class="sidebar-item {{ $t->id === $purchase->id ? 'active' : '' }}">
                        <div class="sidebar-title">{{ $t->good->name }}</div>
                    </a>
                @endforeach
            </div>

            {{-- メインチャットエリア --}}
            <div class="chat-main">
                <h2 class="chat-title">{{ $otherUser->name }} さんとの取引画面</h2>

                {{-- 取引完了モーダル自動表示 --}}
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
                    $transaction = $purchase->transaction;
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
                        <img src="{{ asset($good->image) }}" alt="商品画像" width="100">
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
                            <p class="message-user">
                                <img src="{{ asset('storage/' . $message->user->profile_image) }}" alt="プロフィール画像" class="profile-icon">  
                                {{ $message->user->name }}
                            </p>

                            @if($message->user_id === Auth::id())
                                <div class="message-content js-click-to-edit" data-id="{{ $message->id }}">
                                    {{ $message->message }}
                                </div>

                                @if ($message->image_path)
                                    <img src="{{ asset('storage/' . $message->image_path) }}" alt="添付画像">
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
                                <div class="message-content">{{ $message->message }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- バリデーションエラー --}}
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

                {{-- メッセージ送信フォーム --}}
                <form action="{{ route('chat.send', $purchase->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="message-form-row">
                        <textarea name="message" id="message-body" placeholder="取引メッセージを記入してください" class="message-input"></textarea>

                        <span id="file-name" class="ml-2 text-sm text-gray-600"></span>
                        <input type="file" name="image" id="image" class="hidden">
                        <label for="image" class="image-label">画像を追加</label>

                        <button type="submit" class="icon-submit" aria-label="送信">
                            <img src="{{ asset('images/e99395e98ea663a8400f40e836a71b8c4e773b01.jpg') }}" alt="送信アイコン">
                        </button>
                    </div>
                </form>

                <a href="{{ route('mypage.sell') }}" class="btn btn-sm btn-secondary mb-3">← マイページに戻る</a>
            </div>
        </div>
    </div>

    {{-- JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.js-click-to-edit').forEach(elem => {
                elem.addEventListener('click', () => {
                    const id = elem.dataset.id;
                    elem.style.display = 'none';
                    document.querySelector(`.js-edit-form[data-id="${id}"]`).style.display = 'block';
                });
            });

            document.querySelectorAll('.js-edit-submit').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.id;
                    document.querySelector(`.js-edit-form[data-id="${id}"]`).submit();
                });
            });

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
                window.location.reload();
            }
        });

        document.getElementById('image')?.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || '';
            document.getElementById('file-name').textContent = fileName;
        });
    </script>
@endsection
