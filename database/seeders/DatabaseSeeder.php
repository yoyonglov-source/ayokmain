<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Membuat User Test (Bawaan Laravel)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // 2. Memanggil VenueSeeder (PENTING: Harus di dalam sini)
        $this->call([
            VenueSeeder::class,
        ]);
    }
}