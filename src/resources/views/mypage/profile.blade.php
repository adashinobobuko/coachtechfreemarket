@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}?v={{ time() }}" />
@endsection

@section('content')
<div class="container">
    <h1>プロフィール設定</h1>

    <!-- プロフィール画像のアップロードフォーム -->
    <form action="{{ route('profile.imgupdate') }}" method="POST" class="form" enctype="multipart/form-data" id="image-upload-form">
        @csrf
        <div class="mb-3 image_container">
            @if(Auth::user()->profile_image)
                <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="プロフィール画像" class="mt-3" style="width: 100px; height: 100px; border-radius: 50%;">
            @endif
            <input type="file" id="profile_image" name="profile_image" class="img-form">
            @error('profile_image')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </form>

    <!-- ユーザープロフィールの更新フォーム -->
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="form">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">ユーザー名</label><br>
            <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" class="form-control">
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="postal_code" class="form-label">郵便番号</label><br>
            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', Auth::user()->postal_code) }}" class="form-control">
            @error('postal_code')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">住所</label><br>
            <input type="text" id="address" name="address" value="{{ old('address', Auth::user()->address) }}" class="form-control">
            @error('address')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="building_name" class="form-label">建物名</label><br>
            <input type="text" id="building_name" name="building_name" value="{{ old('building_name', Auth::user()->building_name) }}" class="form-control">
            @error('building_name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <button type="submit" class="form__button-submit">更新する</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('profile_image').addEventListener('change', function() {
            console.log("画像が選択されました:", this.files[0]); // デバッグ用
            document.getElementById('image-upload-form').submit();
        });
    });
</script>
@endsection
