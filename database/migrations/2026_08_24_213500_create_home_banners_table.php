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
        Schema::create('home_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('image');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Default initial banners a1.jpeg to a6.jpeg
        $defaultBanners = [
            ['title' => 'Görsel 1 (a1)', 'image' => '/images/a1.jpeg', 'order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Görsel 2 (a2)', 'image' => '/images/a2.jpeg', 'order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Görsel 3 (a3)', 'image' => '/images/a3.jpeg', 'order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Görsel 4 (a4)', 'image' => '/images/a4.jpeg', 'order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Görsel 5 (a5)', 'image' => '/images/a5.jpeg', 'order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Görsel 6 (a6)', 'image' => '/images/a6.jpeg', 'order' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('home_banners')->insert($defaultBanners);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_banners');
    }
};
