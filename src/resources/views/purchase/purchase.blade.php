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
                    <option value="{{ $paymentType->value }}" {{ old('payment_type') == $paymentType->value ? 'selected' : '' }}>{{ $paymentType->label() }}</option>
                 @endforeach
                </select>
                @error('payment_type')
                <div class="error-message" style="color:red;">{{ $message }}</div>
                @enderror
            </div>

            <hr class="section-divider">

            <div class="shipping-address-section">
                <h3 class="section-title">
                    配送先
                    <a href="{{route ('purchase.editAddress', ['item' => $item->id])}}" class="change-address-link">変更する</a>
                </h3>
                  @if (isset($userAddress))
                <p class="address-zip">〒 {{ $userAddress->post }}</p>
                <p class="address-detail">{{ $userAddress->address }}{{ $userAddress->building }}</p>
                  @else
                <p class="address-zip">住所が登録されていません。</p>
                <p class="address-detail"><a href="{{route ('address.create',  ['item' => $item->id])}}">新しい住所を登録する</a></p>
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
            <form action="{{ route('purchase.store', ['item' => $item->id]) }}" method="post">
                @csrf
                <input type="hidden" name="payment_type" id="hidden_payment_type" value="{{ old('payment_type') }}">
                @if (isset($userAddress))
                    <input type="hidden" name="user_address_id" value="{{ $userAddress->id }}">
                @endif
                <button type="submit" class="purchase-button">購入する</button>
            </form>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentSelect = document.getElementById('payment_type');
        const selectedPaymentMethodSpan = document.getElementById('selected-payment-method');
        const hiddenPaymentTypeInput = document.getElementById('hidden_payment_type');

        if (paymentSelect && selectedPaymentMethodSpan && hiddenPaymentTypeInput) {
            // **修正点: old('payment_type') の値を優先的に使用して初期値を設定**
            const initialPaymentValue = "{{ old('payment_type') }}"; // Bladeから old() の値を取得

            // old() の値があればそれを、なければ現在の paymentSelect.value を使う
            if (initialPaymentValue) {
                // old() の値が存在する場合、対応するオプションを選択状態にする
                paymentSelect.value = initialPaymentValue;
                const selectedOptionText = paymentSelect.options[paymentSelect.selectedIndex].text;
                selectedPaymentMethodSpan.textContent = selectedOptionText;
                hiddenPaymentTypeInput.value = initialPaymentValue;
            } else if (paymentSelect.value) {
                // old() の値がなければ、HTMLに元々設定されている paymentSelect.value を使用
                const selectedOptionText = paymentSelect.options[paymentSelect.selectedIndex].text;
                selectedPaymentMethodSpan.textContent = selectedOptionText;
                hiddenPaymentTypeInput.value = paymentSelect.value;
            } else {
                // どちらも値がなければ、デフォルトの「選択されていません」
                selectedPaymentMethodSpan.textContent = "選択されていません";
                hiddenPaymentTypeInput.value = "";
            }

            // セレクトボックスの変更イベントを監視
            paymentSelect.addEventListener('change', function() {
                const selectedOptionText = this.options[this.selectedIndex].text;
                selectedPaymentMethodSpan.textContent = selectedOptionText;
                hiddenPaymentTypeInput.value = this.value;
            });
        } else {
            console.error('必要なHTML要素が見つかりません。IDを確認してください。');
        }
    });
</script>
@endsection
