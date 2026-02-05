<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\SiteSetting;
use App\Models\SiteSettingTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminSettingController extends Controller
{
    public function edit()
    {
        return Inertia::render('Admin/Settings', [
            'settings' => SiteSetting::allAsArray(),
            'settingTranslations' => SiteSetting::translationsForAdmin(),
            'translatableKeys' => SiteSetting::TRANSLATABLE_KEYS,
            'languages' => Language::getActive()->map(fn ($l) => [
                'code' => $l->code,
                'name' => $l->name,
                'native_name' => $l->native_name,
                'is_default' => $l->is_default,
            ]),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_image' => 'nullable|image|max:20480',
            'hero_cta_text' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|string|max:255',
            'about_text' => 'nullable|string',
            'contact_text' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'favicon' => 'nullable|image|mimes:ico,png,svg,jpg,jpeg|max:2048',
            'logo' => 'nullable|image|max:5120',
            'translations' => 'nullable|array',
            'translations.*' => 'nullable|array',
            'translations.*.*' => 'nullable|string',
        ]);

        $fileFields = ['hero_image', 'favicon', 'logo'];

        foreach ($validated as $key => $value) {
            if (in_array($key, $fileFields) || $key === 'translations') {
                continue;
            }
            SiteSetting::set($key, $value);
        }

        if ($request->hasFile('hero_image')) {
            SiteSetting::set('hero_image', Storage::url(
                $request->file('hero_image')->store('settings', 'public')
            ));
        }

        if ($request->hasFile('logo')) {
            SiteSetting::set('logo', Storage::url(
                $request->file('logo')->store('settings', 'public')
            ));
        }

        if ($request->hasFile('favicon')) {
            SiteSetting::set('favicon', Storage::url(
                $request->file('favicon')->store('settings', 'public')
            ));
        }

        // Save translations
        if ($translations = $request->input('translations')) {
            $settingsByKey = SiteSetting::whereIn('key', SiteSetting::TRANSLATABLE_KEYS)
                ->pluck('id', 'key');

            foreach ($translations as $locale => $fields) {
                foreach ($fields as $key => $value) {
                    if (! isset($settingsByKey[$key])) {
                        continue;
                    }

                    SiteSettingTranslation::updateOrCreate(
                        [
                            'site_setting_id' => $settingsByKey[$key],
                            'locale' => $locale,
                        ],
                        ['value' => $value]
                    );
                }
            }
        }

        return redirect()->back()->with('success', 'Settings updated.');
    }
}
