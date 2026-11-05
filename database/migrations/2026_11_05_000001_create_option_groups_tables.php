<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('option_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('option_group_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_group_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('product_option_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('option_group_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('min_selections')->default(1);
            $table->unsignedTinyInteger('max_selections')->default(1);
            $table->unique(['product_id', 'option_group_id']);
            $table->timestamps();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->json('options')->nullable()->after('product_name');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('options');
        });

        Schema::dropIfExists('product_option_groups');
        Schema::dropIfExists('option_group_values');
        Schema::dropIfExists('option_groups');
    }
};
