<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>COACH TECH</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <a href="/">
                    <img src="{{ asset('storage/images/logo.svg') }}" alt="header_logo">
                </a>
            </div>
                        @if (!Request::is('login') && !Request::is('register'))
                <nav class="header__nav">
                    <form class="search-form" method="GET" action="{{ route('index') }}">
                        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？" class="search-form__input" />
                        @if(request('page') === 'mylist')
                            <input type="hidden" name="page" value="mylist">
                        @endif
                    </form>
                    <ul class="header__nav-list">
                        @auth
                            <li class="header__nav-item">
                                <a href="#" class="header__nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    ログアウト
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                            <li class="header__nav-item">
                                <a href="{{ route('profile') }}" class="header__nav-link">マイページ</a>
                            </li>
                            <li class="header__nav-item">
                                <a href="{{ route('sell.create') }}" class="header__nav-link--button">出品</a>
                            </li>
                        @endauth

                        @guest
                            <li class="header__nav-item">
                                <a href="{{ route('login') }}" class="header__nav-link">ログイン</a>
                            </li>
                            {{-- 未ログイン時のマイページ、出品ボタンはログインページへ遷移させたいので、ここをそのまま残しています --}}
                            <li class="header__nav-item">
                                <a href="{{ route('login') }}" class="header__nav-link">マイページ</a>
                            </li>
                            <li class="header__nav-item">
                                <a href="{{ route('login') }}" class="header__nav-link--button">出品</a>
                            </li>
                        @endguest
                    </ul>
                </nav>
            @endif
        </div>
        </header>


    <main>
        @yield('content')
        @yield('scripts')
    </main>
</body>
</html>
