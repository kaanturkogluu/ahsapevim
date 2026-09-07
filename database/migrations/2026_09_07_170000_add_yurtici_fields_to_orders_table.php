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
            $table->string('yurtici_cargo_key')->nullable()->after('cargo_tracking_code')->index();
            $table->string('yurtici_job_id')->nullable()->after('yurtici_cargo_key');
            $table->string('yurtici_payment_type', 10)->nullable()->default('GO')->after('yurtici_job_id'); // 'GO' (Gönderici) or 'AO' (Alıcı)
            $table->string('yurtici_status')->nullable()->after('yurtici_payment_type'); // 'created', 'cancelled', 'in_transit', 'delivered'
            $table->text('yurtici_response_data')->nullable()->after('yurtici_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'yurtici_cargo_key',
                'yurtici_job_id',
                'yurtici_payment_type',
                'yurtici_status',
                'yurtici_response_data',
            ]);
        });
    }
};
