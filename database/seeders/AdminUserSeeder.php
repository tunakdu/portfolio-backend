<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin kullanıcısını oluştur
        User::firstOrCreate(
            ['email' => 't@a.com'],
            [
                'name' => 'Admin User',
                'email' => 't@a.com',
                'password' => Hash::make('LcvrtVsvs16$'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: t@a.com');
        $this->command->info('Password: LcvrtVsvs16$');
    }
}
