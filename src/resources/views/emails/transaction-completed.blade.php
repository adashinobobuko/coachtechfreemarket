<!DOCTYPE html>
<html>
<head>
    <title>取引完了メール</title>
</head>
<body>
    <h1>取引が完了しました</h1>
    <p>{{ $user->name }} さん、</p>
    <p>以下の取引が完了しました。</p>

    <h2>取引詳細</h2>
    <ul>
        <li><strong>商品名:</strong> {{ $good->name }}</li>
        <li><strong>価格:</strong> ¥{{ number_format($good->price) }}</li>
        <li><strong>購入日時:</strong> {{ $purchase->created_at->format('Y年m月d日 H:i') }}</li>
    </ul>

    <h2>配送先情報</h2>
    <ul>
        <li><strong>氏名:</strong> {{ $address->name }}</li>
        <li><strong>住所:</strong> {{ $address->address }}</li>
    </ul>

    <p>ご利用ありがとうございました！</p>

    <p><a href="{{ route('index') }}">ホームに戻る</a></p>
</body>
</html>
