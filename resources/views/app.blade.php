<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta property="og:title" content="NKNx - The all-in-one tool for NKN miners">
        <meta property="og:site_name" content="NKNx">
        <meta property="og:url" content="https://nknx.org">
        <meta property="og:description" content="NKNx is the all-in-one tool for NKN blockchain network. We make managing and creating your nodes and wallets easy and convenient.">
        <meta property="og:type" content="website">
        <meta property="og:image" content="https://nknx.org/thumbnail.jpg">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@nknX_org">
        <meta name="twitter:title" content="NKNx">
        <meta name="twitter:description" content="NKNx is the all-in-one tool for NKN blockchain network. We make managing and creating your nodes and wallets easy and convenient.">
        <meta name="twitter:image" content="https://nknx.org/thumbnail.jpg">
        <meta name="twitter:image:alt" content="NKNx Logo">

        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">

        <title>NKNx - The all-in-one tool for NKN miners</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/reset.css') }}">
        <link rel="stylesheet" href="{{ mix('css/grid.css') }}">
        <link rel="stylesheet" href="{{ mix('css/feather.css') }}">
        <link rel="stylesheet" href="{{ mix('css/main.css') }}">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">


        <!-- Scripts -->
        <script src="https://polyfill.io/v3/polyfill.min.js?features=smoothscroll,NodeList.prototype.forEach,Promise,Object.values,Object.assign" defer></script>
        <script src =" {{ config('app.btcpayserverurl', '') }}"></script>


        <script src="{{ mix('js/app.js') }}" defer></script>

        @routes
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
