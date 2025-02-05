@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mypage-header">
        @if(Auth::user()->profile_image)
            <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="プロフィール画像" class="mt-3" style="width: 100px; height: 100px; border-radius: 50%;">
        @endif
        <h1>{{ Auth::user()->name }}</h1>
        <a href="{{ route('profile.edit') }}">プロフィールを編集</a>
    </div>
    <div class="item-all">
        <span>出品した商品一覧</span>
            <div class="d-flex gap-3">
                @if(($goods ?? collect())->isNotEmpty())
                    @foreach($goods as $good)
                        <div class="p-3 border text-center">
                            <img src="{{ asset('storage/' . $good->image) }}" alt="商品画像" class="img-fluid">
                            <br>{{ $good->name }}
                        </div>
                    @endforeach
                @else
                    <p>出品した商品はありません。</p>
                @endif
            </div>        
        <span>購入した商品一覧</span>
    </div>
</div>
@endsection
