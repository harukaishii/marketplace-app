@extends('layouts.common')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<main class="main">
    <div class="container purchase-container">
        <div class="purchase-details-wrapper">
            <div class="product-summary">
                <div class="product-summary__image-wrapper">
                    <img src="{{ asset('storage/' . $item->image)}}" alt="{{$item->name}}の商品画像"  class="product-summary__image">
                </div>
                <div class="product-summary__info">
                    <p class="product-summary__name">{{ $item->name}}</p>
                    <p class="product-summary__price">¥{{ number_format($item->price)}}</p>
                </div>
            </div>

            <hr class="section-divider">

            <div class="payment-method-section">
                <h3 class="section-title">支払い方法</h3>
                <select class="payment-select" id="payment_type" name="payment_type">
                    <option value="">選択してください</option>
                    @foreach (\App\Enums\PaymentType::cases() as $paymentType)
                    <option value="{{ $paymentType->value }}">{{ $paymentType->label() }}</option>
                 @endforeach
                </select>
            </div>

            <hr class="section-divider">

            <div class="shipping-address-section">
                <h3 class="section-title">
                    配送先
                    <a href="{{route ('address.change', ['item_id' => $item->id])}}" class="change-address-link">変更する</a>
                </h3>
                  @if (isset($userAddress))
                <p class="address-zip">〒 {{ $userAddress->post }}</p>
                <p class="address-detail">{{ $userAddress->address }}{{ $userAddress->building }}</p>
                  @else
                <p class="address-zip">住所が登録されていません。</p>
                <p class="address-detail"><a href="{{route ('address.create', ['item_id' => $item->id])}}">新しい住所を登録する</a></p>
                  @endif
            </div>
        </div>

        <div class="purchase-sidebar">
            <div class="purchase-info-box">
                <div class="purchase-info-row">
                    <span>商品代金</span>
                    <span class="price">¥{{ number_format($item->price)}}</span>
                </div>
                <div class="purchase-info-row">
                    <span>支払い方法</span>
                    <span id="selected-payment-method">選択されていません</span>
                </div>
            </div>
            <button type="submit" class="purchase-button">購入する</button>
        </div>
    </div>
</main>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // IDが正しく設定されたか確認
        const paymentSelect = document.getElementById('payment_type');
        const selectedPaymentMethodSpan = document.getElementById('selected-payment-method');

        // paymentSelectが存在するか確認し、初期値を設定
        if (paymentSelect && selectedPaymentMethodSpan) { // selectedPaymentMethodSpanも確認
            // 初期選択値がある場合、それを表示
            if (paymentSelect.value) {
                const selectedOptionText = paymentSelect.options[paymentSelect.selectedIndex].text;
                selectedPaymentMethodSpan.textContent = selectedOptionText;
            }

            // セレクトボックスの変更イベントを監視
            paymentSelect.addEventListener('change', function() {
                const selectedOptionText = this.options[this.selectedIndex].text;
                selectedPaymentMethodSpan.textContent = selectedOptionText;
            });
        } else {
            console.error('必要なHTML要素が見つかりません。IDを確認してください。');
        }
    });
</script>
@endsection
@endsection
