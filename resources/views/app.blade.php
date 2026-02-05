<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'SweetVajana') }}</title>

        @php
            $favicon = \App\Models\SiteSetting::get('favicon');
            $activeLanguages = \App\Models\Language::getActive();
            $defaultLang = \App\Models\Language::getDefault();
            $currentPath = request()->getPathInfo();
            $currentLocale = app()->getLocale();
            $defaultCode = $defaultLang?->code ?? 'sk';
        @endphp
        @if($favicon)
            <link rel="icon" href="{{ $favicon }}">
        @endif

        @foreach($activeLanguages as $lang)
            @php
                if ($lang->code === $defaultCode) {
                    $stripped = preg_replace('#^/[a-z]{2}(/|$)#', '/', $currentPath);
                    $hrefUrl = $stripped;
                } else {
                    $stripped = preg_replace('#^/[a-z]{2}(/|$)#', '/', $currentPath);
                    $hrefUrl = '/' . $lang->code . ($stripped === '/' ? '' : $stripped);
                }
            @endphp
            <link rel="alternate" hreflang="{{ $lang->code }}" href="{{ url($hrefUrl) }}">
        @endforeach

        @routes
        @vite(['resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>
