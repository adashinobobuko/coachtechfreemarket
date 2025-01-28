<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtechfreemarket</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    @yield('css')
</head>
<body>
    <div class="header">
        <div class="header-logo">
            <img src="{{ asset('images/logo.svg') }}" alt="coachtechのロゴ">
        </div>
        <div class="searchbar">
            <input type="text" class="searchbar-item">
        </div>
        @if (Auth::check())
        <div class="logged-in">
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
            <a href="{{ route('mypage') }}">マイページ</a>
        </div>
        <div class="tosellform">
            <a href="">出品</a>
        </div>        
    </div>    
    <main>@yield('content')</main>
</body>
</html>