@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="container">
    <h2>住所の変更</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('address.change.update') }}" method="POST">
        @csrf
        <input type="hidden" name="goodsid" value="{{ $good ? $good->id : '' }}">
        @if($good && $good->purchasesAddresses && $good->purchasesAddresses->isNotEmpty())
            <p>郵便番号</p>
            <input type="text" id="postal_code" name="postal_code" class="form-control" 
                value="{{ old('postal_code', $good->purchasesAddresses->first()->postal_code) }}" required>
            <p>住所</p>
            <input type="text" id="address" name="address" class="form-control" 
                value="{{ old('address', $good->purchasesAddresses->first()->address) }}" required>
            <p>建物名</p>
            <input type="text" id="building_name" name="building_name" class="form-control" 
                value="{{ old('building_name', $good->purchasesAddresses->first()->building_name) }}" required>
        @else
            <p>郵便番号</p>
            <input type="text" id="postal_code" name="postal_code" class="form-control" value="" required>
            <p>住所</p>
            <input type="text" id="address" name="address" class="form-control" value="" required>
            <p>建物名</p>
            <input type="text" id="building_name" name="building_name" class="form-control" value="" required>
        @endif
        <button type="submit" class="btn btn-primary">更新</button>
    </form>
</div>
@endsection
