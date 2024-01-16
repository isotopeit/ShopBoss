<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title') || {{ config('app.name') }}</title>
    <meta content="Fahim Anzam Dip" name="author">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/android-chrome-512x512.png') }}">

    @include('pos::includes.main-css')
</head>

<body class="c-app">
    @include('pos::layouts.sidebar')

    <div class="c-wrapper">
        <header class="c-header c-header-light c-header-fixed">
            @include('pos::layouts.header')
            <div class="c-subheader justify-content-between px-3">
                @yield('breadcrumb')
            </div>
        </header>

        <div class="c-body">
            <main class="c-main">
                @yield('content')
            </main>
        </div>

        @include('pos::layouts.footer')
    </div>

    @include('pos::includes.main-js')
</body>
</html>
