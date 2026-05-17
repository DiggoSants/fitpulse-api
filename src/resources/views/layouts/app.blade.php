<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
       @stack('styles')
       @if (file_exists(public_path('hot')))
           @vite(['resources/css/app.css', 'resources/js/app.js'])
       @else
           @php
               $viteManifestPath = public_path('build/manifest.json');
               $viteManifest = file_exists($viteManifestPath)
                   ? json_decode(file_get_contents($viteManifestPath), true)
                   : [];
           @endphp
           @isset($viteManifest['resources/css/app.css']['file'])
               <link rel="stylesheet" href="{{ asset('build/' . $viteManifest['resources/css/app.css']['file']) }}">
           @endisset
           @vite('resources/js/app.js')
       @endif

        <style>
            body { background: #0a0a0a !important; color: #fff !important; font-family: 'Montserrat', sans-serif !important; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen" style="background:#0a0a0a;">
            @include('layouts.navigation')

            @isset($header)
                <header style="background:rgba(255,255,255,0.03); border-bottom:1px solid rgba(255,255,255,0.08);">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
