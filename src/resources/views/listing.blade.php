@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/listing.css') }}?v={{ time() }}" />
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    @if (isset($errors) && $errors->any())
        <div class="mb-4 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <h1 class="text-2xl font-bold text-center mb-6">商品の出品</h1>
    
    <form action="{{ route('sellform.store') }}" method="POST" enctype="multipart/form-data" class="listing-form max-w-lg mx-auto bg-white p-6 rounded-lg shadow">
        @csrf
        
        <!-- 商品画像 -->
        <div class="mb-4">
            <label class="block">商品画像</label>
            <div class="imgbox">
                <label for="file-upload" class="custom-file-upload">
                    画像を選択する
                </label>
                <input id="file-upload" type="file" name="image" class="img-form hidden">
                @error('image')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>
        
        <!-- 商品詳細ラベル-->
        <h2>商品の詳細</h2>

        <!-- 商品の詳細 -->
        <div class="mb-4">
            <label class="block text-gray-700">カテゴリー</label>
            <div class="category-container flex-wrap">
            @foreach($categories as $category)
            <label class="category-item">
                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                    {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                {{ $category->name }}
            </label>
            @endforeach
            </div>
            @error('category_ids')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <!-- 商品の状態 -->
        <div class="mb-4">
            <label class="block text-gray-700">商品の状態</label>
            <select name="condition" class="selecter">
                <option value="良好" {{ old('condition') == '良好' ? 'selected' : '' }}>良好</option>
                <option value="目立った傷や汚れ無し" {{ old('condition') == '目立った傷や汚れ無し' ? 'selected' : '' }}>目立った傷や汚れ無し</option>
                <option value="やや傷や汚れあり" {{ old('condition') == 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
                <option value="状態が悪い" {{ old('condition') == '状態が悪い' ? 'selected' : '' }}>状態が悪い</option>
            </select>
            @error('condition')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <!-- 商品名 -->
        <div class="mb-4">
            <label class="block text-gray-700">商品名</label>
            <input type="text" name="name" class="w-full border p-2 rounded mt-1" required value="{{ old('name') }}">
            @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <!-- ブランド名 -->
        <div class="mb-4">
            <label class="block text-gray-700">ブランド名</label>
            <input type="text" name="brand" class="w-full border p-2 rounded mt-1" value="{{ old('brand') }}">
            @error('brand')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <!-- 商品の説明 -->
        <div class="mb-4">
            <label class="block text-gray-700">商品の説明</label>
            <textarea name="description" class="description__form" rows="4" required>{{ old('description') }}</textarea>
            @error('description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <!-- 販売価格 -->
        <div class="mb-4">
            <label class="block text-gray-700">販売価格</label>
            <div class="input-wrapper">
            <input type="number" name="price" class="price-field" required min="0" value="{{ old('price') }}">

            </div>
            @error('price')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <button type="submit" class="listing-button">出品する</button>
    </form>
</div>
@endsection

<script>

    document.addEventListener("DOMContentLoaded", function () {
    const categoryItems = document.querySelectorAll(".category-item input");

    categoryItems.forEach(input => {
        input.addEventListener("change", function () {
            if (this.checked) {
                this.parentElement.classList.add("selected");
            } else {
                this.parentElement.classList.remove("selected");
            }
        });
    });
    });

</script>