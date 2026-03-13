<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('billing_address');
            $table->string('company_id')->nullable()->after('company_name');
            $table->string('vat_number')->nullable()->after('company_id');
            $table->string('invoice_path')->nullable()->after('vat_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'company_id', 'vat_number', 'invoice_path']);
        });
    }
};
