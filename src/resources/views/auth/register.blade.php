@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register__content">
  <div class="register-form__heading">
    <h3>会員登録</h3>
  </div>
  <form class="form" action="{{ route('register') }}" method="post">
    @csrf
    <div class="form__group">
      <label for="name" class="form__label">ユーザー名</label>
      <input type="text" id="name" name="name" value="{{ old('name') }}" class="form__input">
      @error('name')
        <div class="form__error">{{ $message }}</div>
      @enderror
    </div>

    <div class="form__group">
      <label for="email" class="form__label">メールアドレス</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" class="form__input">
      @error('email')
        <div class="form__error">{{ $message }}</div>
      @enderror
    </div>

    <div class="form__group">
      <label for="password" class="form__label">パスワード</label>
      <input type="password" id="password" name="password" class="form__input">
      @error('password')
        <div class="form__error">{{ $message }}</div>
      @enderror
    </div>

    <div class="form__group">
      <label for="password" class="form__label">確認用パスワード</label>
      <input type="password" id="password" name="password_confirmation" class="form__input">
      @error('password')
        <div class="form__error">{{ $message }}</div>
      @enderror
    </div>

    <div class="form__group">
      <button type="submit" class="button--primary">登録する</button>
    </div>
  </form>

  <div class="tologin">
    <a href="{{ route('login') }}">ログインはこちら</a>
  </div>
</div>
@endsection