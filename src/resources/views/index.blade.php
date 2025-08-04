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
                    <a href="{{ route('index', ['page' => 'mylist', 'keyword' => request('keyword')]) }}">マイリスト</a>
                </li>
            </ul>
        </nav>

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


@endsection
