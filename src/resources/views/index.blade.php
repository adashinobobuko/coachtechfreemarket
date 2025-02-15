@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}?v={{ time() }}" />
@endsection

@section('content')
    <body>
        <div class="container mt-4">
            <!-- タブメニュー -->
            <div class="tab-menu">
                <a href="{{ route('index') }}" class="tab-link {{ $activeTab === 'recommend' ? 'active' : '' }}">おすすめ</a>
                <a href="{{ route('mylist') }}" class="tab-link {{ $activeTab === 'mylist' ? 'active' : '' }}">マイリスト</a>
            </div>

            <!-- タブの内容 -->
            <div class="tab-content {{ $activeTab === 'recommend' ? 'active' : '' }}">
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

            <div class="tab-content {{ $activeTab === 'mylist' ? 'active' : '' }}">
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
    </body>
@endsection
