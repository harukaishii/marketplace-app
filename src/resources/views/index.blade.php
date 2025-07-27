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
                <li class="main-nav__item"><a href="#" class="main-nav__link">おすすめ</a></li>
                <li class="main-nav__item">
                    <a href="{{ route('index', ['page' => 'mylist', 'listed_by' => Auth::id()]) }}"
                       class="main-nav__link {{ request('page') == 'mylist' ? 'main-nav__link--active' : '' }}">
                        マイリスト
                    </a>
                </li>
            </ul>
        </nav>

        <div class="product-list">
            @foreach ($items as $item)
                @if(Auth::check() && $item->listed_by === Auth::id())
                    @continue
                @endif

            <a href="{{ route('item.show', ['item_id' => $item->id]) }}" class="product-item-link">
                <div class="product-item">
                    <div class="product-item__image-wrapper">
                        <img src="{{ asset('storage/' . $item->image)}}" alt="{{$item->name}}の商品画像" class="product-item__image">
                    </div>
                    <div class="product-info-container">
                        <p class="product-item__name">
                            {{ $item->name }}
                        </p>
                        @if ($item->status === \App\Enums\ItemStatus::SOLD)
                            <span class="product-item__status--sold">sold</span>
                        @endif
                    </div>
                </div>
            </a>

            @endforeach
        </div>
    </div>
</main>


@endsection
