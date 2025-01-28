@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mypage-header">
        <span>画像</span>
        <h1>ユーザー名</h1>
        <a href="{{ route('profile.edit') }}">プロフィールを編集</a>
    </div>
    <div class="item-all">
        <span>出品した商品一覧</span>
        <span>購入した商品一覧</span>
    </div>
</div>
@endsection
