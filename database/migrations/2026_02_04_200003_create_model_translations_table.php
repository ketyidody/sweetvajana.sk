<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_translations', function (Blueprint $table) {
            $table->id();
            $table->string('translatable_type');
            $table->unsignedBigInteger('translatable_id');
            $table->string('locale', 5);
            $table->string('field', 50);
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['translatable_type', 'translatable_id', 'locale', 'field'], 'model_translations_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_translations');
    }
};
