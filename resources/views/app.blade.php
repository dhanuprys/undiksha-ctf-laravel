<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- Standard SEO Meta Tags -->
        <meta name="description" content="Ganesha CTF Platform - Kompetisi Capture The Flag Cybersecurity modern untuk menguji keterampilan hacking dan keamanan siber Anda.">
        <meta name="keywords" content="CTF, Capture The Flag, Cybersecurity, Hacking, Ganesha CTF, Keamanan Siber">
        <meta name="author" content="Ganesha CTF Platform">
        
        <!-- OpenGraph Meta Tags for Social Media Sharing -->
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ config('app.name', 'Ganesha CTF Platform') }}">
        <meta property="og:title" content="{{ config('app.name', 'Ganesha CTF Platform') }}">
        <meta property="og:description" content="Tantang kemampuan cybersecurity kamu di Ganesha CTF Platform. Berbagai misi seru menanti!">
        <meta property="og:image" content="{{ asset('images/ganesha-ctf-platofrm-banner.png') }}">
        <meta property="og:image:alt" content="Ganesha CTF Platform Banner">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        
        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ config('app.name', 'Ganesha CTF Platform') }}">
        <meta name="twitter:description" content="Tantang kemampuan cybersecurity kamu di Ganesha CTF Platform. Berbagai misi seru menanti!">
        <meta name="twitter:image" content="{{ asset('images/ganesha-ctf-platofrm-banner.png') }}">

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
</html>
