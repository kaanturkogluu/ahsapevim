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
        if (!Schema::hasColumn('orders', 'cargo_sms_sent_at')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->timestamp('cargo_sms_sent_at')->nullable()->after('yurtici_response_data');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'cargo_sms_sent_at')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('cargo_sms_sent_at');
            });
        }
    }
};
