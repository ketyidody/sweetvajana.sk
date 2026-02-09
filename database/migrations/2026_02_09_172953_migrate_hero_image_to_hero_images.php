<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $old = DB::table('site_settings')->where('key', 'hero_image')->first();

        if ($old) {
            $images = $old->value ? json_encode([$old->value]) : '[]';

            DB::table('site_settings')->updateOrInsert(
                ['key' => 'hero_images'],
                ['value' => $images]
            );

            DB::table('site_settings')->where('key', 'hero_image')->delete();
        } else {
            DB::table('site_settings')->updateOrInsert(
                ['key' => 'hero_images'],
                ['value' => '[]']
            );
        }
    }

    public function down(): void
    {
        $row = DB::table('site_settings')->where('key', 'hero_images')->first();

        if ($row) {
            $images = json_decode($row->value, true) ?: [];
            $firstImage = $images[0] ?? null;

            DB::table('site_settings')->updateOrInsert(
                ['key' => 'hero_image'],
                ['value' => $firstImage]
            );

            DB::table('site_settings')->where('key', 'hero_images')->delete();
        }
    }
};
