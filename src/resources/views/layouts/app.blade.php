<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtechfreemarket</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('css')
</head>
<body>
    <div class="header">
        <div class="header-logo">
            <a href="{{ route('index') }}" class="header-logo_a">
                <img src="{{ asset('images/logo.svg') }}" alt="coachtechのロゴ">
            </a>
        </div>
        <form class="search-form" action="{{ route('search') }}" method="GET">
            <input type="text" class="search-form__item-input" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？">
            <input type="hidden" name="tab" value="recommend"> <!-- 検索時に強制的にrecommend -->
        </form>

        @if (Auth::check())
        <div class="logged-out">
             <a href=""  onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                   ログアウト
             </a>
             <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
             </form>
         </div>
        @else
        <div class="tologin">
            <a href="{{ route('login') }}">ログイン</a>
        </div>
        @endif
        <div class="tomypage">
            <a href="{{ route('mypage.sell') }}">マイページ</a>
        </div>
        <div class="tosellform">
            <a href="{{ route('sellform') }}">出品</a>
        </div>        
    </div>    
    <main>@yield('content')</main>
</body>
</html>