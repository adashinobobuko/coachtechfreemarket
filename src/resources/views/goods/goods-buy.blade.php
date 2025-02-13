@extends('layouts.app')

@section('content')
<div class="container">
    <h2>購入画面</h2>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <img src="{{ asset('storage/' . $good->image) }}" alt="商品画像" class="img-fluid border">
                </div>
                <div class="col-md-8">
                    <h2>{{ $good->name }}</h2>
                    <h3 class="text-danger">¥{{ number_format($good->price) }}（税込）</h3>
                    <hr>
                    <form action="{{ route('purchase.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="good_id" value="{{ $good->id }}">
                        <h5>支払い方法</h5>
                            <select name="payment_method" class="form-control" required>
                                <option value="">選択してください</option>
                                <option value="コンビニ払い">コンビニ払い</option>
                                <option value="カード払い">カード払い</option>
                            </select>
                        <hr>
                        <h5>配送先</h5>
                        @if(Auth::check() && Auth::user()->address)
                            <p>
                                〒 {{ Auth::user()->postal_code ?? '未登録' }}<br>
                                {{ Auth::user()->address ?? '住所未登録' }}<br>
                                {{ Auth::user()->building_name ?? '' }}
                            </p>
                        @elseif ($errors->has('address'))  <!-- ここを elseif に変更 -->
                            <div class="alert alert-danger">
                                {{ $errors->first('address') }}
                            </div>
                        @else
                            <div class="alert alert-warning">
                                住所が登録されていません。プロフィールページで登録してください。
                            </div>
                        @endif
                        <a href="{{ route('address.change.form') }}" class="btn btn-link">変更する</a>
                        <hr>
                        <button typr="submit" class="btn btn-danger btn-lg btn-block">購入する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
