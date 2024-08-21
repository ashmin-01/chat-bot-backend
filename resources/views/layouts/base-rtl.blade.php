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
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="public/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="public/assets/img/favicon.png">

  <title>
    Django Dashboard Black - @yield('title') | AppSeed
  </title>

  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet" />
  <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
  <!-- Nucleo Icons -->
  <link href="public/assets/css/nucleo-icons.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link href="public/assets/css/black-dashboard.css?v=1.0.0" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-rtl/3.4.0/css/bootstrap-rtl.css" rel="stylesheet" />
  <link href="public/assets/css/theme-switcher.css" rel="stylesheet"/>
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="public/assets/demo/demo.css" rel="stylesheet" />

    <!-- Specific Page CSS goes HERE  -->
    @yield('stylesheets')

</head>
<body class=" rtl menu-on-right ">

    <div class="wrapper">

        @include('includes/sidebar-rtl.html')

        <div class="main-panel">

        @include ('includes/navigation-rtl.html')

        @yield('content')

        @include('includes/footer.html')

        </div>

    </div>

    @include('includes/fixed-plugin.html')

    @include('includes/scripts.html')

    <!-- Specific Page JS goes HERE  -->
    @yield('javascripts')

</body>
</html>
