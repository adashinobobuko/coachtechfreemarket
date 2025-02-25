@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endsection

@section('content')
<div class="container">
    <h2>録していただいたメールアドレスに認証メールを送付しました。メール認証を完了してください。</h2>

    @if (session('email'))
        <p>登録したメールアドレス: <strong>{{ session('email') }}</strong></p>
    @endif

    <form action="{{ route('resend.email') }}" method="POST">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">
        <button type="submit" class="btn btn-warning">認証メールを再送する</button>
    </form>

    <p><a href="{{ route('login') }}">ログインページに戻る</a></p>
</div>
@endsection
