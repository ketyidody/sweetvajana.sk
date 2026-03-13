<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'SweetVajana') }}</title>

        @php
            $siteSettings = \App\Models\SiteSetting::allAsArray(app()->getLocale());
            $favicon      = $siteSettings['favicon'] ?? null;
            $siteName     = $siteSettings['site_name'] ?? config('app.name', 'SweetVajana');
            $metaDesc     = $siteSettings['meta_description'] ?? '';
            $heroImages   = is_array($siteSettings['hero_images'] ?? null) ? $siteSettings['hero_images'] : [];
            $ogImage      = count($heroImages) > 0 ? $heroImages[0] : '';
            $canonicalUrl = request()->url();
            $activeLanguages = \App\Models\Language::getActive();
            $defaultLang  = \App\Models\Language::getDefault();
            $currentPath  = request()->getPathInfo();
            $currentLocale = app()->getLocale();
            $defaultCode  = $defaultLang?->code ?? 'sk';
        @endphp

        @if($favicon)
            <link rel="icon" href="{{ $favicon }}">
        @endif

        <link rel="canonical" href="{{ $canonicalUrl }}">
        <meta name="description" content="{{ $metaDesc }}">

        {{-- Open Graph --}}
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ $siteName }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <meta property="og:title" content="{{ $siteName }}">
        <meta property="og:description" content="{{ $metaDesc }}">
        @if($ogImage)
            <meta property="og:image" content="{{ $ogImage }}">
        @endif

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
        <meta name="twitter:title" content="{{ $siteName }}">
        <meta name="twitter:description" content="{{ $metaDesc }}">
        @if($ogImage)
            <meta name="twitter:image" content="{{ $ogImage }}">
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
