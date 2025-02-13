@extends('layouts.app')

@section('content')
<div class="container">
    <h2>住所変更フォーム</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('address.change.update') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="postal_code">郵便番号:</label>
            <input type="text" id="postal_code" name="postal_code" class="form-control" value="{{ old('postal_code', $user->postal_code) }}" required>
            @error('postal_code') <p class="text-danger">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="address">住所:</label>
            <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $user->address) }}" required>
            @error('address') <p class="text-danger">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="building_name">建物名:</label>
            <input type="text" id="building_name" name="building_name" class="form-control" value="{{ old('building_name', $user->building_name) }}" required>
            @error('building_name') <p class="text-danger">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn btn-primary">更新</button>
    </form>
</div>
@endsection
