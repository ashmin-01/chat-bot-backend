<!--
=========================================================
* * Black Dashboard - v1.0.1
=========================================================

* Product Page: https://www.creative-tim.com/product/black-dashboard
* Copyright 2019 Creative Tim (https://www.creative-tim.com)


* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="public/assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="public/assets/img/favicon.png">

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
    <link href="{{ asset('assets/demo/demo.css') }}" rel="stylesheet"/>

    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Specific Page CSS goes HERE  -->
    @yield('stylesheets')

</head>
<body class="">

    <div class="wrapper">

        @include('includes.navigation-fullscreen')

        <div class="container-fluid">

            @yield('content')

            @include('includes.footer-fullscreen')

        </div>

    </div>

    @include('includes.fixed-plugin')

    @include('includes.scripts')

    <!-- Specific Page JS goes HERE  -->
    @yield('javascripts')

</body>
</html>
