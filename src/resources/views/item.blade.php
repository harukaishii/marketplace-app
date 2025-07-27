@extends('layouts.common')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<main class="main">
    <div class="item-detail-container">
        <h2 class="item-detail__title">{{ $item->name}}</h2>

        <div class="item-detail__wrapper">
            <div class="item-detail__image-area">
                <div class="item-detail__image-placeholder">
                    <img src="{{ asset('storage/' . $item->image)}}" alt="{{$item->name}}の商品画像" class="item-detail__image">
                </div>
            </div>
            <div class="item-detail__info-area">
                <p class="item-detail__brand">{{ $item->brand_name ?? ''}}</p>
                <p class="item-detail__price">¥{{ number_format($item->price)}}<span>（税込）</span></p>
                <div class="item-detail__actions">
                    <div class="item-detail__action-icons">
                        <span class="icon-wrapper">
                            <i class="far fa-star"><img src="{{ asset('storage/images/星アイコン8.png') }}" alt="favorite_icon"></i>{{ $item->favorites->count()}}
                        </span>
                        <span class="icon-wrapper">
                            <i class="far fa-comment"><img src="{{ asset('storage/images/ふきだしのアイコン.png') }}" alt="comment_icon"></i> {{ $item->comments->count()}}
                        </span>
                    </div>
                    <a href="{{ route('item.purchase',['item_id' => $item->id]) }}" class="btn btn--purchase">購入手続きへ</a>
                </div>

                <div class="item-detail__section">
                    <h3 class="section__title">商品説明</h3>
                    <p class="section__content">
                        {{$item->detail}}
                    </p>
                </div>

                <div class="item-detail__section">
                    <h3 class="section__title">商品の情報</h3>
                    <table class="item-detail__table">
                        <tr>
                            <th>カテゴリー</th>
                            <td>
                                @foreach($item-> categories as $category)
                                <span class="tag">{{ $category->name }}</span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>商品の状態</th>
                            <td>{{$item->condition->label()}}</td>
                        </tr>
                    </table>
                </div>

                <div class="item-detail__section">
                    <h3 class="section__title">コメント({{ $item->comments->count()}})</h3>
                    @forelse ($item->comments as $comment)
                    <div class="comment">
                        <div class="comment__user-icon">
                            <img src="{{ Storage::url($comment->user->image) }}" alt="{{$comment->user->name}}のアイコン画像" class="comment__user-image">
                        </div>
                        <div class="comment__body">
                            <p class="comment__user-name">{{ $comment->user->name ?? '名無し'}}</p>
                            <p class="comment__text">{{ $comment->comment }}</p>
                            <p class="comment__date">{{ $comment->created_at->format('Y/m/d H:i') }}</p>
                        </div>
                    </div>
                    @empty
                    <p>まだコメントはありません</p>
                    @endforelse
                </div>

                <div class="item-detail__section">
                    <h3 class="section__title">商品へのコメント</h3>
                    {{-- @auth {{-- ログインしているユーザーのみコメントフォームを表示 --}}
                    <form action="{{ route('item.comment.store', ['itemId' => $item->id]) }}" method="POST">
                        @csrf {{-- CSRFトークンを含める --}}
                        <textarea class="comment-textarea" name="comment" placeholder="コメントを入力してください。">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="btn btn--comment-submit">コメントを送信する</button>
                    </form>
                    {{-- @else
                    <p>コメントを投稿するには<a href="{{ route('login') }}">ログイン</a>が必要です。</p>
                    {{-- @endauth--}}
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
