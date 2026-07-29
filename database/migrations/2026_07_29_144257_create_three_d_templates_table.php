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
        Schema::create('three_d_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('wood_type')->default('Ceviz');
            $table->float('width')->default(20);
            $table->float('height')->default(25);
            $table->float('depth')->default(3);
            $table->float('thickness')->default(3);
            $table->boolean('has_top')->default(true);
            $table->boolean('has_bottom')->default(true);
            $table->boolean('has_left')->default(true);
            $table->boolean('has_right')->default(true);
            $table->float('inner_width')->default(13);
            $table->float('inner_height')->default(18);
            $table->float('inner_depth')->default(2.5);
            $table->float('inner_border')->default(1.0);
            $table->float('pos_x')->default(0);
            $table->float('pos_y')->default(0);
            $table->float('bump_scale')->default(0.08);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('three_d_templates');
    }
};
