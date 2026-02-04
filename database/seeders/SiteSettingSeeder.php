<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'Sweet Vajana',
            'hero_title' => 'Handcrafted Cakes & Sweets',
            'hero_subtitle' => 'Discover our delicious selection of freshly baked cakes, pastries, and sweet treats made with love.',
            'hero_image' => null,
            'hero_cta_text' => 'Shop Now',
            'phone' => '+421 123 456 789',
            'email' => 'info@sweetvajana.sk',
            'instagram_url' => '',
            'facebook_url' => '',
            'about_text' => 'Handcrafted cakes and sweets made fresh daily with premium ingredients and lots of love.',
            'contact_text' => 'Get in touch with us for custom orders and inquiries.',
            'meta_description' => 'Sweet Vajana - Handcrafted cakes, cookies, and sweet treats made with love.',
            'footer_text' => 'Sweet Vajana. All rights reserved.',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
