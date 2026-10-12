<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix any orders previously updated to 'delivered' status to the standard 'completed' status
        DB::table('orders')
            ->where('status', 'delivered')
            ->update(['status' => 'completed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed as 'completed' is the standard status across the system
    }
};
