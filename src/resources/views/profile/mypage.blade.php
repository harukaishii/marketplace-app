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
                {{-- 画像が登録されていない場合のプレースホルダー --}}
                @if (empty($user->image))
                    <div class="profile-image__placeholder"></div>
                @else
                    {{-- 画像が登録されている場合 --}}
                    <img src="{{ Storage::url($user->image) }}" alt="Current Profile Image" class="profile-image__actual">
                @endif
            </div>
            <h2 class="user-profile__name">{{$user->name}}</h2>
            <form method="GET" action="{{route('profile.edit')}}">
                <button class="user-profile__edit-button">プロフィールを編集</button>
            </form>
        </div>

        <nav class="profile-tabs">
            <ul class="profile-tabs__list">
                {{-- 出品した商品タブ --}}
                <li class="profile-tabs__item">
                    <a href="{{ route('profile', ['page' => 'sell']) }}" class="profile-tabs__link {{ $page === 'sell' ? 'profile-tabs__link--active' : '' }}">出品した商品</a>
                </li>
                {{-- 購入した商品タブ --}}
                <li class="profile-tabs__item">
                    <a href="{{ route('profile', ['page' => 'buy']) }}" class="profile-tabs__link {{ $page === 'buy' ? 'profile-tabs__link--active' : '' }}">購入した商品</a>
                </li>
            </ul>
        </nav>

        <div class="product-list">
            @forelse ($items as $item)
                <div class="product-item">
                    <div class="product-item__image-wrapper">
                        @if ($item->image)
                            <img src="{{ asset('storage/' . $item->image)}}" alt="{{$item->name}}の商品画像" class="product-item__image">
                        @else
                            <div class="product-item__no-image">No Image</div>
                        @endif
                    </div>
                    <p class="product-item__name">{{ $item->name }}</p>
                </div>
            @empty
                <p>該当の商品はありません。</p>
            @endforelse
        </div>
    </div>
</main>
@endsection
