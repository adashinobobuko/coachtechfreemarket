@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}?v={{ time() }}" />
@endsection

@section('content')
<div class="container mypage-container">
    <div class="mypage-header">
        <div class="profile-container">
            @if(Auth::user()->profile_image)
                <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="プロフィール画像" class="profile-image">
            @else
                <img src="{{ asset('images/default-user.png') }}" alt="デフォルトプロフィール画像" class="profile-image">
            @endif
            <h2 class="username">{{ Auth::user()->name }}</h2>
            <a href="{{ route('profile.edit') }}" class="btn btn-outline-danger edit-profile-btn">プロフィールを編集</a>
        </div>
    </div>

    <div class="container mt-4">
            <!-- タブメニュー -->
        <div class="tab-menu">
            <a href="{{ route('mypage.sell') }}" class="tab-link {{ $activeTab === 'sell' ? 'active' : '' }}">出品した商品</a>
            <a href="{{ route('mypage.buy') }}" class="tab-link {{ $activeTab === 'buy' ? 'active' : '' }}">購入した商品</a>
        </div>
    </div>

    <div class="tab-content {{ $activeTab === 'sell' ? 'active' : '' }}">
        @if(isset($goods) && count($goods) > 0)
            <div class="d-flex gap-3 product-grid">
                @foreach($goods as $good)
                    <div class="p-3 position-relative">
                        <a href="{{ route('goods.show', $good['id']) }}">
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $good['image']) }}" alt="商品画像" class="product-image">
                                @if(isset($good['is_sold']) && $good['is_sold'] == true)
                                    <div class="sold-out-overlay">sold</div>
                                @endif
                            </div>
                            <p class="product-name">{{ $good['name'] }}</p>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="no-items text-center">出品した商品はありません。</p>
        @endif
    </div>

    <div class="tab-content {{ $activeTab === 'buy' ? 'active' : '' }}">
        <div class="d-flex gap-3">
            @if(isset($purchases) && $purchases->isNotEmpty())
                @foreach($purchases as $purchase)
                    <div class="p-3 border text-center position-relative">
                        <a href="{{ route('goods.show', $purchase->good->id) }}">
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $purchase->good->image) }}" alt="商品画像" class="product-image">
                                @if($purchase->isSold())
                                    <div class="sold-out-overlay">sold</div>
                                @endif
                            </div>
                            <p class="product-name">{{ $purchase->good->name }}</p>
                        </a>
                    </div>
                @endforeach
            @else
                <p class="no-items text-center">購入した商品はありません。</p>
            @endif
        </div>
    </div>
@endsection
