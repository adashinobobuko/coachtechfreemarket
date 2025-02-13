@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}?v={{ time() }}" />
@endsection

@section('content')
<body>

    <div class="container mt-4">
        <!-- タブメニュー -->
        <div class="tab-menu">
            <button class="tab-link active" data-tab="tab1">おすすめ</button>
            <button class="tab-link" data-tab="tab2">マイリスト</button>
        </div>

        <!-- タブの内容 -->
        <div class="tab-content active" id="tab1">
            <h3>おすすめ商品</h3>
            <div class="d-flex gap-3">
                @if(isset($goods) && $goods->isNotEmpty())
                    @foreach($goods as $good)
                        @if(!Auth::check() || Auth::id() !== $good->user_id)
                            <div class="p-3 border text-center position-relative">
                                <a href="{{ route('goods.show', $good->id) }}">
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $good->image) }}" alt="商品画像" class="product-image">
                                        @if($good->is_sold_out)
                                            <div class="sold-out-overlay">SOLD OUT</div>
                                        @endif
                                    </div>
                                    <br>{{ $good->name }}
                                </a>
                            </div>
                        @endif
                    @endforeach
                @else
                    <p>おすすめの商品はありません。</p>
                @endif
            </div>
        </div>
            <div class="tab-content" id="tab2">
                <h3>マイリスト</h3>
                <div class="d-flex gap-3">
                    @if(Auth::check() && isset($favorites) && $favorites->isNotEmpty())
                        @foreach($favorites as $favorite)
                            @if($favorite->good) <!-- good リレーションが null でないことを確認 -->
                                <div class="p-3 border text-center">
                                    <a href="{{ route('goods.show', $favorite->good->id) }}">
                                        <img src="{{ asset('storage/' . $favorite->good->image) }}" alt="商品画像" class="product-image">
                                    </a>
                                    <br>{{ $favorite->good->name }}
                                </div>
                            @endif
                        @endforeach
                    @else
                        <p>マイリストには商品がありません。</p>
                    @endif
                </div>
            </div>
    </div>

    <!-- JavaScriptでタブ切り替え -->
    <script>
        document.querySelectorAll(".tab-link").forEach(button => {
            button.addEventListener("click", function() {
                let tabId = this.getAttribute("data-tab");

                // すべてのタブメニューを非アクティブにする
                document.querySelectorAll(".tab-link").forEach(btn => btn.classList.remove("active"));

                // クリックされたタブをアクティブにする
                this.classList.add("active");

                // すべてのタブコンテンツを非表示にする
                document.querySelectorAll(".tab-content").forEach(content => content.classList.remove("active"));

                // 選択されたタブのコンテンツを表示する
                document.getElementById(tabId).classList.add("active");
            });
        });
    </script>

</body>
@endsection
