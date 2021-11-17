<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(Auth::check())
    <meta name="user-token" content="{{ Auth::user()->id}}">
    @else
    <meta name="user-token" content="0">
    @endif

    <title>{{ config('app.name', 'Document Management System') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="icon" sizes="32x32" href="/svg/favicon.ico">

</head>

<body>
    <div id="app" @guest class="no-login" @endguest>
        @guest
        @else
        @include('partials.navbar')
        @endguest
        <div class="container-fluid">
            <div class="row">
                @guest
                <div class="col-md-12 no-login">
                    @yield('content')
                </div>
                @else
                <br />
                <div class="col-md-2">
                    @if(Auth::user()->is_owner == 0)
                    @include('partials.sidebar')
                    @else
                    @include('partials.sidebar_owner')
                    @endif
                </div>
                <div class="col-md-10">
                    @yield('content')
                </div>
                @endguest
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}" defer></script>

    <script defer src="{{ asset('js/fontawesome_svg.js') }}" crossorigin="anonymous"></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

</body>

</html>