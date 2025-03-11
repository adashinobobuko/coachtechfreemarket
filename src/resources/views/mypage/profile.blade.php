@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}?v={{ time() }}" />
@endsection

@section('content')
<div class="container">
    <h1>プロフィール設定</h1>
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="form">
        @csrf
        <div class="mb-3 image_container">
            @if(Auth::user()->profile_image)
                <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="プロフィール画像" class="mt-3" style="width: 100px; height: 100px; border-radius: 50%;">
            @endif
            <input type="file" id="profile_image" name="profile_image" class="img-form">
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">ユーザー名</label><br>
            <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label for="postal_code" class="form-label">郵便番号</label><br>
            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', Auth::user()->postal_code) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">住所</label><br>
            <input type="text" id="address" name="address" value="{{ old('address', Auth::user()->address) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label for="building_name" class="form-label">建物名</label><br>
            <input type="text" id="building_name" name="building_name" value="{{ old('building_name', Auth::user()->building_name) }}" class="form-control">
        </div>
        <div class="mb-4">
            <button type="submit" class="form__button-submit">更新する</button>
        </div>
    </form>
</div>
@endsection
