@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}?v={{ time() }}" />
@endsection

@section('content')
<div class="container mypage-container">
    <div class="mypage-header text-center">
        @if(Auth::user()->profile_image)
            <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="プロフィール画像" class="profile-image">
        @else
            <img src="{{ asset('images/default-user.png') }}" alt="デフォルトプロフィール画像" class="profile-image">
        @endif
        <h2 class="username">{{ Auth::user()->name }}</h2>
        <a href="{{ route('profile.edit') }}" class="btn btn-outline-danger edit-profile-btn">プロフィールを編集</a>
    </div>

    <div class="mypage-tabs text-center">
        <button class="tab-link active" onclick="switchTab('sell')">出品した商品</button>
        <button class="tab-link" onclick="switchTab('bought')">購入した商品</button>
    </div>

    <div id="sell" class="item-list">
        <div class="row row-cols-2 row-cols-md-4 g-3">
            @if(($goods ?? collect())->isNotEmpty())
                @foreach($goods as $good)
                    <div class="col">
                        <div class="card product-card">
                            <a href="{{ route('goods.show', $good->id) }}">
                                <img src="{{ asset('storage/' . $good->image) }}" alt="商品画像" class="card-img-top product-image">
                                <div class="card-body text-center">
                                    <p class="product-name">{{ $good->name }}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted">出品した商品はありません。</p>
            @endif
        </div>
    </div>

    <div id="bought" class="item-list" style="display: none;">
        <p class="text-muted">購入した商品はありません。</p>
    </div>
</div>

<script>
function switchTab(tab) {
    document.getElementById('sell').style.display = (tab === 'sell') ? 'block' : 'none';
    document.getElementById('bought').style.display = (tab === 'bought') ? 'block' : 'none';

    document.querySelectorAll('.tab-link').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`button[onclick="switchTab('${tab}')"]`).classList.add('active');
}
</script>
@endsection
