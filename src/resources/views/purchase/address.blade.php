@extends('layouts.common')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<main class="main">
    <div class="container address-change-container">
        <h2 class="page-title">住所の変更</h2>
        <form class="address-form">
            <div class="form-group">
                <label for="postalCode" class="form-label">郵便番号</label>
                <input type="text" id="postalCode" class="form-input" value="{{ old('post', $userAddress->post ?? '') }}">
            </div>
            <div class="form-group">
                <label for="address" class="form-label">住所</label>
                <input type="text" id="address" class="form-input" value="{{ old('address', $userAddress->address ?? '') }}">
            </div>
            <div class="form-group">
                <label for="buildingName" class="form-label">建物名</label>
                <input type="text" id="buildingName" class="form-input" value="{{ old('building', $userAddress->building ?? '') }}">
            </div>
            <button type="submit" class="update-button">更新する</button>
        </form>
    </div>
</main>
@endsection
