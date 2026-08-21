<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'sort_order')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('sort_order')->default(0)->index()->after('id');
            });

            // Populate existing products with sequential sort_order numbers
            $products = DB::table('products')->orderBy('id', 'asc')->get();
            $order = 1;
            foreach ($products as $p) {
                DB::table('products')->where('id', $p->id)->update(['sort_order' => $order]);
                $order++;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'sort_order')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};
