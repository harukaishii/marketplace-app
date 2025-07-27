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
            <nav class="header__nav">
                <form class="header__nav-search">
                    <input type="search" placeholder="なにをお探しですか？" class="search-form__input" />
                </form>
                <ul class="header__nav-list">
                    <li class="header__nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="header__nav-button">ログアウト</button>
                        </form>
                    </li>
                    <li class="header__nav-item">
                        <a href="/mypage" class="header__nav-link">マイページ</a>
                    </li>
                    <li class="header__nav-item">
                        <a href="/sell" class="header__nav-link header__nav-link--button">出品</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
        @yield('scripts')
    </main>
</body>
</html>
