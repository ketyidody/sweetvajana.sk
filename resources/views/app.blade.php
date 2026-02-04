<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'SweetVajana') }}</title>

        @php
            $favicon = \App\Models\SiteSetting::get('favicon');
        @endphp
        @if($favicon)
            <link rel="icon" href="{{ $favicon }}">
        @endif

        @routes
        @vite(['resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>
