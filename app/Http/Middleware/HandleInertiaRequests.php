<?php

namespace App\Http\Middleware;

use App\Models\Language;
use App\Models\SiteSetting;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'site_settings' => fn () => Schema::hasTable('site_settings')
                ? SiteSetting::allAsArray()
                : [],
            'cartItemsCount' => fn () => collect($request->session()->get('cart', []))->sum('quantity'),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'locale' => fn () => app()->getLocale(),
            'defaultLocale' => fn () => Language::getDefault()?->code ?? 'sk',
            'languages' => fn () => Schema::hasTable('languages')
                ? Language::getActive()->map(fn ($l) => [
                    'code' => $l->code,
                    'name' => $l->name,
                    'native_name' => $l->native_name,
                    'is_default' => $l->is_default,
                ])
                : [],
            'translations' => fn () => Schema::hasTable('translations')
                ? Translation::forLocale(app()->getLocale())
                : [],
        ];
    }
}
