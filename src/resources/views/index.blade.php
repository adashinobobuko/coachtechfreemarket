@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}?v={{ time() }}" />
@endsection

@section('content')
    <body>
        <div class="flashmessage">
            @if(session('message'))
            <div class="flashmessage__success">
                {{ session('message') }}
            </div>
            @endif
        </div>
        <div class="container mt-4">
            <!-- タブメニュー -->
            <div class="tab-menu">
                <a href="{{ route('index', ['tab' => 'recommend', 'keyword' => request('keyword')]) }}" 
                   class="tab-link {{ $activeTab === 'recommend' ? 'active' : '' }}">
                    おすすめ
                </a>
                <a href="{{ route('mylist', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}" 
                   class="tab-link {{ $activeTab === 'mylist' ? 'active' : '' }}">
                    マイリスト
                </a>
            </div>

            <!-- タブの内容 -->
            <div class="tab-content {{ $activeTab === 'recommend' ? 'active' : '' }}">
                @if(isset($goods) && $goods->isNotEmpty())
                    <div class="d-flex gap-3">
                        @foreach($goods as $good)
                            <div class="p-3 border text-center position-relative">
                                <a href="{{ route('goods.show', $good->id) }}">
                                    <div class="position-relative image-wrapper">
                                        <img src="{{ asset('storage/' . $good->image) }}" alt="商品画像" class="product-image">
                                        @if($good->isSold())
                                            <div class="sold-out-overlay">sold</div>
                                        @endif
                                    </div>
                                    <p class="product-name">{{ $good->name }}</p>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="no-items text-center">おすすめの商品はありません。</p>
                @endif
            </div>

            <div class="tab-content {{ $activeTab === 'mylist' ? 'active' : '' }}">
                <div class="d-flex gap-3">
                    @if(Auth::check() && isset($favorites) && $favorites->isNotEmpty())
                        @foreach($favorites as $favorite)
                            @if($favorite->good) <!-- good リレーションが null でないことを確認 -->
                                <div class="p-3 position-relative">
                                    <a href="{{ route('goods.show', $favorite->good->id) }}">
                                        <div class="position-relative image-wrapper">
                                            <img src="{{ asset('storage/' . $favorite->good->image) }}" alt="商品画像" class="product-image">
                                            @if($good->isSold())
                                                <div class="sold-out-overlay">sold</div>
                                            @endif
                                        </div>
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

    </body>
@endsection
