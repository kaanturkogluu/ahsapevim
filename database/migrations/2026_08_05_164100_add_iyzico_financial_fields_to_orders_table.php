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
            $table->decimal('paid_price', 10, 2)->nullable()->after('total_amount'); // Bankanın çektiği tutar
            $table->integer('installment')->default(1)->after('paid_price'); // Taksit sayısı
            $table->decimal('merchant_payout_amount', 10, 2)->nullable()->after('installment'); // Esnafın net hak ediş miktarı
            $table->string('card_family')->nullable()->after('merchant_payout_amount'); // Kart ailesi (Bonus, World vb.)
            $table->string('card_last_four', 4)->nullable()->after('card_family'); // Kart son 4 hane
            $table->string('payment_error_reason')->nullable()->after('status'); // İptal veya başarısızlık sebebi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'paid_price',
                'installment',
                'merchant_payout_amount',
                'card_family',
                'card_last_four',
                'payment_error_reason',
            ]);
        });
    }
};
