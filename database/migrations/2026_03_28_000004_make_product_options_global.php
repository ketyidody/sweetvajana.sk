<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('corpuses', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });

        Schema::table('cream_flavors', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });

        Schema::table('additions', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('corpuses', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('cream_flavors', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('additions', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }
};
