<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_methods', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->text('settings')->nullable();
            $table->timestamps();
        });

        $id = DB::table('delivery_methods')->insertGetId([
            'key' => 'store_pickup',
            'name' => 'Osobný odber',
            'description' => 'Objednávku si vyzdvihnete osobne v našej predajni.',
            'price' => 0,
            'is_active' => true,
            'position' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (['name' => 'Store Pickup', 'description' => 'Collect your order in person at our shop.'] as $field => $value) {
            DB::table('model_translations')->insert([
                'translatable_type' => 'App\\Models\\DeliveryMethod',
                'translatable_id' => $id,
                'locale' => 'en',
                'field' => $field,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('model_translations')->where('translatable_type', 'App\\Models\\DeliveryMethod')->delete();
        Schema::dropIfExists('delivery_methods');
    }
};
