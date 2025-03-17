@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/goods-buy.css') }}">
<script src="https://js.stripe.com/v3/"></script>
<meta name="stripe-key" content="{{ config('services.stripe.public') }}">
@endsection

@section('content')
<div class="container">
    <div class="card flex-item1">
        <div class="flashmessage">
            @if(session('message'))
            <div class="flashmessage__success">
                {{ session('message') }}
            </div>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <img src="{{ asset('storage/' . $good->image) }}" alt="商品画像" class="img-fluid border">
                </div>
                <div class="col-md-8">
                    <h2>{{ $good->name }}</h2>
                    <h3 class="text-danger">¥{{ number_format($good->price) }}（税込）</h3>
                    <hr>
                    <form id="payment-form" action="{{ route('checkout.process', ['goodsid' => $good->id]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="good_id" value="{{ $good->id }}">
                        <input type="hidden" id="payment-method" name="payment_method">
                        <input type="hidden" id="stripe-token" name="stripe_token">

                        <h5>支払い方法</h5>
                        <select id="payment-select" name="payment_method" class="form-control" required>
                            <option value="">選択してください</option>
                            <option value="コンビニ払い">コンビニ払い</option>
                            <option value="カード払い">カード払い</option>
                        </select>

                        <hr>
                        <h5>配送先</h5>
                        @php
                            $user = Auth::user();
                            $address = $good->purchasesAddresses->first() ?? null;
                            $postal_code = $address ? $address->postal_code : ($user->postal_code ?? '未登録');
                            $user_address = $address ? $address->address : ($user->address ?? '住所未登録');
                            $building_name = $address ? $address->building_name : ($user->building_name ?? '');
                        @endphp

                        <p>
                            〒 {{ $postal_code }}<br>
                            {{ $user_address }}<br>
                            {{ $building_name }}
                        </p>

                        @if (!$user->postal_code || !$user->address)
                            <div class="alert alert-warning">
                                住所が登録されていません。プロフィールページで登録してください。
                            </div>
                        @endif
                        <a href="{{ route('address.change.form', ['goodsid' => $good->id]) }}">変更する</a>
                        <hr>

                        <!-- Stripe カード入力欄 -->
                        <div id="card-element" class="form-control"></div>
                        <div id="card-errors" class="text-danger"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card flex-item2">
        <div class="displaygrid">
            <table>
                <tr>
                    <td>商品代金</td>
                    <td class="text-bold"> ¥{{ number_format($good->price) }}（税込）</td>
                </tr>
                <tr>
                    <td>支払方法</td>
                    <td id="payment-method-display">支払</td>
                </tr>
            </table>
        </div>
        <button id="checkout-button" type="button" class="btn btn-danger btn-lg btn-block">購入する</button>
    </div>
</div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentSelect = document.querySelector('select[name="payment_method"]');
        const paymentDisplay = document.getElementById('payment-method-display');

        paymentSelect.addEventListener('change', function() {
            paymentDisplay.textContent = paymentSelect.value;
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        const stripeKey = document.querySelector('meta[name="stripe-key"]').getAttribute("content");
        const stripe = Stripe(stripeKey);
        const checkoutButton = document.getElementById("checkout-button");

        checkoutButton.addEventListener("click", function () {
            const paymentMethod = document.getElementById("payment-select").value;
            const goodId = "{{ $good->id }}";
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            if (paymentMethod === "カード払い") {
                fetch(`{{ route('checkout.process', ['goodsid' => $good->id]) }}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(session => {
                    if (!session.sessionId) {
                        throw new Error("セッションIDが取得できませんでした。");
                    }
                    return stripe.redirectToCheckout({ sessionId: session.sessionId });
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("決済処理中にエラーが発生しました: " + error.message);
                });
            } else if (paymentMethod === "コンビニ払い") {
                document.getElementById("payment-form").action = "{{ route('purchase.store') }}";
                document.getElementById("payment-form").submit();
            } else {
                alert("支払い方法を選択してください。");
            }
        });
    });
</script>
