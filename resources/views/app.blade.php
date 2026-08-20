<!-- DEDANLABS here! -->

<!DOCTYPE html>

<!-- JANGAN BUAT ANEH-ANEH DI WEB INI YA BANG :) -->

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>

<!-- SWIPER.. JANGAN MENCURI!!! -->

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-NCFGSZPK5R"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-NCFGSZPK5R');
    </script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon and App Icons -->
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Ganesha CTF" />
    <link rel="manifest" href="/site.webmanifest" />
    <!-- Standard SEO Meta Tags -->
    <meta name="description"
        content="Ganesha CTF Platform - Kompetisi Capture The Flag Cybersecurity modern untuk menguji keterampilan hacking dan keamanan siber Anda.">
    <meta name="keywords" content="CTF, Capture The Flag, Cybersecurity, Hacking, Ganesha CTF, Keamanan Siber">
    <meta name="author" content="Ganesha CTF Platform">

    <!-- OpenGraph Meta Tags for Social Media Sharing -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name', 'Ganesha CTF Platform') }}">
    <meta property="og:title" content="{{ config('app.name', 'Ganesha CTF Platform') }}">
    <meta property="og:description"
        content="Tantang kemampuan cybersecurity kamu di Ganesha CTF Platform. Berbagai misi seru menanti!">
    <meta property="og:image" content="{{ asset('images/ganesha-ctf-platform-banner.png') }}">
    <meta property="og:image:alt" content="Ganesha CTF Platform Banner">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ config('app.name', 'Ganesha CTF Platform') }}">
    <meta name="twitter:description"
        content="Tantang kemampuan cybersecurity kamu di Ganesha CTF Platform. Berbagai misi seru menanti!">
    <meta name="twitter:image" content="{{ asset('images/ganesha-ctf-platform-banner.png') }}">

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    <x-inertia::head>
        <title>{{ config('app.name', 'Laravel') }}</title>
    </x-inertia::head>
</head>

<body class="font-sans antialiased">
    <x-inertia::app />
</body>

<!-- YOK BISA YOK MAIN SEHAT -->

</html>