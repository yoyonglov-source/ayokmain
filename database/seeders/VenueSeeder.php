<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venue;
use App\Models\OperatingHour;
use App\Models\User;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil user pertama (Test User yang dibuat di DatabaseSeeder)
        $user = User::first();

        // Jika user tidak ditemukan (jaga-jaga kalau seeder user belum jalan)
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Admin Gracia',
                'email' => 'admin@gracia.com',
            ]);
        }

        // 2. Buat Data Venue dengan menyertakan user_id
        $venue = Venue::create([
            'user_id' => $user->id, // INI KUNCI PERBAIKANNYA
            'name'    => 'Gracia Sport Center',
            'slug'    => 'gracia-sport-center',
            'address' => 'Jl. Puncak No. 123',
            'category' => 'Sport Center',
            'city' => 'Malang',
            'phone_number' => '087863430919',
        ]);

        // 3. Buat Data Jam Operasional Default
        $days = [0, 1, 2, 3, 4, 5, 6];

        foreach ($days as $day) {
            OperatingHour::create([
                'venue_id'   => $venue->id,
                'day'        => $day,
                'open_time'  => '07:00',
                'close_time' => '23:00',
                'is_closed'  => false
            ]);
        }
    }
}