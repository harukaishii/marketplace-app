@extends('layouts.common')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<main class="main">
    <div class="container">
        <div class="user-profile">
            <div class="profile-image__wrapper">
                @if (empty($user->image))
                    <div class="profile-image__placeholder"></div>
                @else
                    <img src="{{ Storage::url($user->image) }}" alt="Current Profile Image" class="profile-image__actual">
                @endif
            </div>
            <div class="user-profile__info">
                <h2 class="user-profile__name">{{ $user->name }}</h2>
                {{-- 評価平均の星表示 --}}
                @php
                    $averageRating = $user->getAverageRating();
                @endphp
                @if($averageRating !== null)
                    <div class="user-profile__rating">
                        <div class="rating-stars">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $averageRating)
                                    <span class="star star--filled">★</span>
                                @else
                                    <span class="star star--empty">☆</span>
                                @endif
                            @endfor
                        </div>
                    </div>
                @endif
            </div>
            <form method="GET" action="{{ route('profile.edit') }}">
                <button class="user-profile__edit-button">プロフィールを編集</button>
            </form>
        </div>

        <nav class="profile-tabs">
            <ul class="profile-tabs__list">
                {{-- 出品した商品タブ --}}
                <li class="profile-tabs__item">
                    <a href="{{ route('profile', ['page' => 'sell']) }}" class="profile-tabs__link {{ $page === 'sell' ? 'profile-tabs__link--active' : '' }}">
                        出品した商品
                    </a>
                </li>
                {{-- 購入した商品タブ --}}
                <li class="profile-tabs__item">
                    <a href="{{ route('profile', ['page' => 'buy']) }}" class="profile-tabs__link {{ $page === 'buy' ? 'profile-tabs__link--active' : '' }}">
                        購入した商品
                    </a>
                </li>
                {{-- 取引中タブ --}}
                <li class="profile-tabs__item">
                    <a href="{{ route('profile', ['page' => 'transaction']) }}" class="profile-tabs__link {{ $page === 'transaction' ? 'profile-tabs__link--active' : '' }}">
                        取引中
                        @if(isset($totalUnreadCount) && $totalUnreadCount > 0)
                            <span class="unread-badge">{{ $totalUnreadCount }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </nav>

        <div class="product-list">
            {{-- 全てのタブで同じ表示形式 --}}
            @forelse ($items as $item)
                @if ($page === 'transaction')
                    {{-- 取引中の商品：クリック可能 + 未読バッジ --}}
                    <a href="{{ route('transactions.show', $item->id) }}" class="product-item">
                        <div class="product-item__image-wrapper">
                            {{-- 未読通知マーク --}}
                            @php
                                $unreadCount = $item->getUnreadCount(auth()->id());
                            @endphp
                            @if ($unreadCount > 0)
                                <span class="unread-notification">{{ $unreadCount }}</span>
                            @endif

                            @if ($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}の商品画像" class="product-item__image">
                            @else
                                <div class="product-item__no-image">No Image</div>
                            @endif
                        </div>
                        <p class="product-item__name">{{ $item->name }}</p>
                    </a>
                @else
                    {{-- 出品した商品 / 購入した商品：通常表示 --}}
                    <div class="product-item">
                        <div class="product-item__image-wrapper">
                            @if ($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}の商品画像" class="product-item__image">
                            @else
                                <div class="product-item__no-image">No Image</div>
                            @endif
                        </div>
                        <p class="product-item__name">{{ $item->name }}</p>
                    </div>
                @endif
            @empty
                <p class="empty-message">
                    @if ($page === 'transaction')
                        取引中の商品はありません。
                    @else
                        該当の商品はありません。
                    @endif
                </p>
            @endforelse
        </div>
    </div>
</main>
@endsection
