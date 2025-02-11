@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h2>購入が完了しました！</h2>
    <p>ご購入ありがとうございました。</p>
    <a href="{{ route('index') }}" class="btn btn-primary">トップページへ戻る</a>
</div>
@endsection
