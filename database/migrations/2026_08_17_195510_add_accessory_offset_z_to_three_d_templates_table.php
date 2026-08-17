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
        Schema::table('three_d_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('three_d_templates', 'accessory_offset_z')) {
                $table->float('accessory_offset_z')->default(0)->after('accessory_offset_y');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('three_d_templates', function (Blueprint $table) {
            if (Schema::hasColumn('three_d_templates', 'accessory_offset_z')) {
                $table->dropColumn('accessory_offset_z');
            }
        });
    }
};
