@extends("layouts.app")

@section("css")
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section("content")
    <div class="transaction-chat">
        <div class="chat-wrapper">

            {{-- サイドバー --}}
            <div class="sidebar">
                @foreach ($otherTransactions as $t)
                    <a href="{{ route('chat.buyer', $t->id) }}"
                       class="sidebar-item {{ $t->id === $purchase->id ? 'active' : '' }}">
                        <div class="sidebar-title">{{ $t->good->name }}</div>
                    </a>
                @endforeach
            </div>

            {{-- メインチャットエリア --}}
            <div class="chat-main">
                <h2 class="chat-title">{{ $otherUser->name }} さんとの取引画面</h2>

                <form id="complete-form" action="{{ route('transactions.complete', ['purchase' => $purchase->id]) }}" method="POST">
                    @csrf
                    <button type="submit" id="complete-button">取引を完了する</button>
                </form>

                @php
                    $transaction = $purchase->transaction;
                @endphp

                @if ($transaction->status === 'completed')
                    <p>取引完了</p>
                @endif

                {{-- 評価モーダル（取引完了後に表示） --}}
                @if(request('completed'))
                {{-- 最初は非表示にしておく --}}
                <div id="complete-modal" class="hidden">
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

                {{-- メッセージ一覧 --}}
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

                                <form action="{{ route('chat.update', $message->id) }}" method="POST"
                                      class="js-edit-form" data-id="{{ $message->id }}" style="display: none;">
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

                {{-- エラーメッセージ --}}
                @if($errors->any())
                    <div class="error-messages">
                        @foreach ($errors->all() as $error)
                            <p class="error">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- フラッシュメッセージ --}}
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

                <a href="{{ route('mypage.sell') }}" class="mypagebtn">← マイページに戻る</a>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        document.getElementById('image')?.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || '';
            document.getElementById('file-name').textContent = fileName;
        });
        document.addEventListener('DOMContentLoaded', () => {
            const completeModal = document.getElementById('complete-modal');
            if (window.location.search.includes('completed=1')) {
                completeModal?.classList.remove('hidden');
            }

            const modalClose = document.getElementById('modal-close');
            modalClose?.addEventListener('click', () => {
                completeModal?.classList.add('hidden');
            });
        });

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

        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
        const completeModal = document.getElementById('complete-modal');

        // クエリパラメータに ?completed=1 があるときだけ表示
        if (window.location.search.includes('completed=1')) {
            completeModal?.classList.remove('hidden');
        }
    });
    </script>
@endsection
