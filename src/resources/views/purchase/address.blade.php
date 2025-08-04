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

        {{-- エラーメッセージの表示（追加） --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 成功メッセージの表示（追加） --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form class="address-form" action="{{ route('purchase.updateAddress') }}" method="POST">
            @csrf

            {{-- 住所変更後に元の購入フォームに戻るためにitem_idをhiddenで渡す--}}
            @if (isset($item) && $item)
                <input type="hidden" name="item_id" value="{{ $item->id }}">
            @endif
            

            <div class="form-group">
                <label for="postalCode" class="form-label">郵便番号</label>
                <input type="text" id="postalCode" name="post" class="form-input" value="{{ old('post', $userAddress->post ?? '') }}" required autocomplete="postal-code">
            </div>
            <div class="form-group">
                <label for="address" class="form-label">住所</label>
                <input type="text" id="address" name="address" class="form-input" value="{{ old('address', $userAddress->address ?? '') }}" required autocomplete="street-address">
            </div>
            <div class="form-group">
                <label for="buildingName" class="form-label">建物名</label>
                <input type="text" id="buildingName" name="building" class="form-input" value="{{ old('building', $userAddress->building ?? '') }}" autocomplete="address-line3">
            </div>
            <button type="submit" class="update-button">更新する</button>
            @if (isset($item) && $item)
                <a href="{{ route('purchase.showPurchaseForm', ['item' => $item->id]) }}" class="back-link">購入画面に戻る</a>
            @endif
        </form>
    </div>
</main>
@endsection
