<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $activeCodes = Language::getActiveCodes();
        $defaultCode = Language::getDefault()?->code ?? 'sk';
        $locale = $request->route('locale');

        if ($locale) {
            if (! in_array($locale, $activeCodes)) {
                abort(404);
            }

            if ($locale === $defaultCode) {
                $path = preg_replace('#^/' . preg_quote($locale, '#') . '(/|$)#', '/', $request->getPathInfo());
                $query = $request->getQueryString();

                return redirect($path . ($query ? '?' . $query : ''), 301);
            }

            app()->setLocale($locale);
            URL::defaults(['locale' => $locale]);
        } else {
            if ($request->user()?->locale && in_array($request->user()->locale, $activeCodes)) {
                app()->setLocale($request->user()->locale);
            } else {
                app()->setLocale($defaultCode);
            }

            URL::defaults(['locale' => null]);
        }

        return $next($request);
    }
}
