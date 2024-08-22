<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">

    <title>
        Django Dashboard Black - @yield('title') | AppSeed
    </title>

    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet"/>
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet"/>
    <!-- CSS Files -->
    <link href="{{ asset('assets/css/black-dashboard.css?v=1.0.0') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/theme-switcher.css') }}" rel="stylesheet"/>
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link href="{{ asset('assets/demo/demo.css') }}" rel="stylesheet"/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Specific Page CSS goes HERE  -->
    @yield('stylesheets')

</head>
<body class="">

    <div class="wrapper">

        @include('includes.sidebar')

        <div class="main-panel">

            @include('includes.navigation')

            @yield('content')

            @include('includes.footer')

        </div>
    </div>

    @include('includes.fixed-plugin')

    @include('includes.scripts')

    <!-- Specific Page JS goes HERE  -->
    @yield('javascripts')

</body>
</html>
