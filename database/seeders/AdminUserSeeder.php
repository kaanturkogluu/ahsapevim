<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@ahsapevim.com');
        $password = env('ADMIN_PASSWORD', 'ChangeMe2024!');

        if (!\App\Models\User::where('email', $email)->exists()) {
            \App\Models\User::create([
                'name'     => 'AhşapEvim Admin',
                'email'    => $email,
                'password' => bcrypt($password),
                'is_admin' => true,
            ]);

            $this->command->info("✅ Admin kullanıcı oluşturuldu: {$email}");
        } else {
            $this->command->info("ℹ️ Admin kullanıcı zaten mevcut: {$email}");
        }
    }
}
