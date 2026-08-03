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
            $table->foreignId('shipping_company_id')->nullable()->after('payment_id')->constrained('shipping_companies')->nullOnDelete();
            $table->string('cargo_tracking_code')->nullable()->after('shipping_company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shipping_company_id']);
            $table->dropColumn(['shipping_company_id', 'cargo_tracking_code']);
        });
    }
};
