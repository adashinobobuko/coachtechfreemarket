@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}" />
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
                        <div class="p-3 border text-center">
                            <a href="{{ route('goods.show', $good->id) }}">
                                <img src="{{ asset('storage/' . $good->image) }}" alt="商品画像" class="img-fluid">
                                <br>{{ $good->name }}
                            </a>
                        </div>
                    @endforeach
                @else
                    <p>出品した商品はありません。</p>
                @endif
            </div>
        </div>
        <div class="tab-content" id="tab2">
            <h3>マイリスト</h3>
            <div class="d-flex gap-3">
                <div class="p-3 border text-center">商品画像<br>商品名</div>
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

</body
@endsection
