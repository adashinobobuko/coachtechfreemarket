@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-center mb-6">商品の出品</h1>
    
    <form action="{{ route('sellform.store') }}" method="POST" enctype="multipart/form-data" class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow">
        @csrf
        
        <!-- エラーメッセージ表示 -->
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- 商品画像 -->
        <div class="mb-4">
            <label class="block text-gray-700">商品画像</label>
            <input type="file" name="image" class="w-full border p-2 rounded mt-1">
            @error('image')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <!-- 商品の詳細 -->
        <div class="mb-4">
            <label class="block text-gray-700">カテゴリー</label>
            <div class="flex flex-wrap gap-2 mt-2">
                @foreach(['ファッション', '家電', 'インテリア', 'レディース', 'メンズ', 'コスメ', '本', 'ゲーム', 'スポーツ', 'キッチン', 'ハンドメイド', 'アクセサリー', 'おもちゃ', 'ベビー・キッズ'] as $category)
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="category[]" value="{{ $category }}" class="mr-2"> {{ $category }}
                    </label>
                @endforeach
            </div>
            @error('category')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <!-- 商品の状態 -->
        <div class="mb-4">
            <label class="block text-gray-700">商品の状態</label>
            <select name="condition" class="w-full border p-2 rounded mt-1">
                <option value="">選択してください</option>
                <option value="新品・未使用">新品・未使用</option>
                <option value="未使用に近い">未使用に近い</option>
                <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                <option value="傷や汚れあり">傷や汚れあり</option>
                <option value="全体的に状態が悪い">全体的に状態が悪い</option>
            </select>
            @error('condition')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <!-- 商品名と説明 -->
        <div class="mb-4">
            <label class="block text-gray-700">商品名</label>
            <input type="text" name="name" class="w-full border p-2 rounded mt-1" required>
            @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700">商品の説明</label>
            <textarea name="description" class="w-full border p-2 rounded mt-1" rows="4" required></textarea>
            @error('description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <!-- 販売価格 -->
        <div class="mb-4">
            <label class="block text-gray-700">販売価格</label>
            <input type="number" name="price" class="w-full border p-2 rounded mt-1" required min="0">
            @error('price')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <button type="submit" class="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600">出品する</button>
    </form>
</div>
@endsection
