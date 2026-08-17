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
        if (!Schema::hasColumn('three_d_templates', 'has_accessory')) {
            Schema::table('three_d_templates', function (Blueprint $table) {
                $table->boolean('has_accessory')->default(false)->after('bump_scale');
                $table->string('accessory_type')->default('street_lamp')->after('has_accessory');
                $table->string('accessory_position')->default('right')->after('accessory_type');
                $table->float('accessory_offset_x')->default(0)->after('accessory_position');
                $table->float('accessory_offset_y')->default(0)->after('accessory_offset_x');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('three_d_templates', function (Blueprint $table) {
            $table->dropColumn(['has_accessory', 'accessory_type', 'accessory_position', 'accessory_offset_x', 'accessory_offset_y']);
        });
    }
};
