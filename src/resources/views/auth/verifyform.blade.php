@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endsection

@section('content')
<div class="container">

    <h2>登録していただいたメールアドレスに認証メールを送付しました。</h2>
    <h2>メール認証を完了してください。</h2>

    <a class="verify__button" href="{{ route('verify.email', ['token' => $user->email_verification_token ?? '']) }}">
        認証はこちらから
    </a>
    <!--セッションを使いログインを行う-->

    <form action="{{ route('resend.email') }}" method="POST">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">
        <button type="submit" class="btn btn-warning">認証メールを再送する</button>
    </form>

</div>
@endsection
