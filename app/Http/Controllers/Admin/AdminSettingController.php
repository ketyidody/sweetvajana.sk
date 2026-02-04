<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminSettingController extends Controller
{
    public function edit()
    {
        return Inertia::render('Admin/Settings', [
            'settings' => SiteSetting::allAsArray(),
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
        ]);

        $fileFields = ['hero_image', 'favicon', 'logo'];

        foreach ($validated as $key => $value) {
            if (in_array($key, $fileFields)) {
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

        return redirect()->back()->with('success', 'Settings updated.');
    }
}
