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
        <header class="header-logo">
            <img src="{{ asset('images/logo.svg') }}" alt="coachtechのロゴ">
        </header>
    </div>
    <main>@yield('content')</main>
</body>
</html>