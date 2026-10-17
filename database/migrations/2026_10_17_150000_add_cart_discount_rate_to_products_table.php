<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'cart_discount_rate')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('cart_discount_rate')->default(10)->after('original_price');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'cart_discount_rate')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('cart_discount_rate');
            });
        }
    }
};
