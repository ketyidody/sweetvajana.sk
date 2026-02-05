<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        Language::updateOrCreate(['code' => 'sk'], [
            'name' => 'Slovak',
            'native_name' => 'Slovensky',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        Language::updateOrCreate(['code' => 'en'], [
            'name' => 'English',
            'native_name' => 'English',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }
}
