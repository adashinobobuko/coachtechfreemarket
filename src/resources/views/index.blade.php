@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}?v={{ time() }}" />
@endsection

@section('content')
    <body>
        <!-- デバッグ -->
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
                <a href="{{ route('search', ['tab' => 'recommend', 'keyword' => request('keyword')]) }}" 
                class="tab-link {{ $activeTab === 'recommend' ? 'active' : '' }}">
                    おすすめ
                </a>
                <a href="{{ route('search', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}" 
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
                    <p class="no-items text-center">マイリストには商品がありません。</p>
                @endif
            </div>

    </body>
@endsection
