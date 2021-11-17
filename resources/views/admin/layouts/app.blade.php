<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(Auth::guard('admin')->check())
    <meta name="user-token" content="{{ Auth::guard('admin')->user()->id}}">
    @else
    <meta name="user-token" content="admin">
    @endif

    <title>{{ config('app.name', 'Document Management') }}</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="icon" sizes="32x32" href="/svg/favicon.ico">
</head>

<body>
    <div id="app">
        @include('admin.partials.navbar')

        <div class="container-fluid">
            <div class="row">
                @guest('admin')
                @else
                <div class="col-md-3">
                    @include('admin.partials.sidebar')
                </div>
                @endguest
                <div class="col-md-8">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script defer src="{{ asset('js/fontawesome_svg.js') }}" crossorigin="anonymous"></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

</body>

</html>