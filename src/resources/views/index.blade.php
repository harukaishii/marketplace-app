@extends('layouts.common')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<main class="main">
    <div class="container">
        <nav class="main-nav">
            <ul class="main-nav__list">
                <li class="main-nav__item"><a href="/" class="main-nav__link">おすすめ</a></li>
                <li class="main-nav__item">
                    <a href="{{ route('index', ['page' => 'mylist', 'keyword' => request('keyword')]) }}">マイリスト</a>
                </li>
            </ul>
        </nav>

        {{-- 成功メッセージ --}}
        @if (session('success'))
            <div class="alert alert-success" id="successMessage">
                {{ session('success') }}
            </div>
        @endif

        {{-- エラーメッセージ --}}
        @if (session('error'))
            <div class="alert alert-error" id="errorMessage">
                {{ session('error') }}
            </div>
        @endif

        {{-- 通常のメッセージ（既存のもの） --}}
        @if (session('message'))
            <div class="alert alert-success" id="message">
                {{ session('message') }}
            </div>
        @endif

        <div class="product-list">
            @forelse ($items as $item)
                <a href="{{ route('item.show', ['item_id' => $item->id]) }}" class="product-item-link">
                    <div class="product-item">
                        <div class="product-item__image-wrapper">
                            <img src="{{ asset('storage/' . $item->image)}}" alt="{{$item->name}}の商品画像" class="product-item__image">
                        </div>
                        <div class="product-info-container">
                            <p class="product-item__name">{{ $item->name }}</p>
                            @if ($item->status === \App\Enums\ItemStatus::SOLD)
                                <span class="product-item__status--sold">sold</span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                @if (request('page') === 'mylist' && !Auth::check())
                    <p>マイリストを表示するにはログインしてください</p>
                @elseif (request('page') === 'mylist')
                    <p>マイリストに商品がありません</p>
                @else
                    <p>現在、表示できる商品がありません</p>
                @endif
            @endforelse
        </div>
    </div>
</main>

{{-- メッセージを自動的に消すスクリプト --}}
@if (session('success') || session('error') || session('message'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    // すべてのアラートメッセージを取得
    const alerts = document.querySelectorAll('.alert');

    alerts.forEach(alert => {
        // 3秒後にフェードアウト開始
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';

            // フェードアウト完了後に要素を削除
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 3000);
    });
});
</script>
@endif

@endsection
