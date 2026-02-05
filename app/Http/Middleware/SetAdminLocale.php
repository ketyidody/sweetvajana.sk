<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $activeCodes = Language::getActiveCodes();
        $defaultCode = Language::getDefault()?->code ?? 'sk';

        if ($request->user()?->locale && in_array($request->user()->locale, $activeCodes)) {
            app()->setLocale($request->user()->locale);
        } else {
            app()->setLocale($defaultCode);
        }

        return $next($request);
    }
}
