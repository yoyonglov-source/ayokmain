<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom pengaman di tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('password'); // 'user', 'owner', 'superadmin'
            $table->string('ktp_number', 16)->nullable()->after('role');
            $table->string('ktp_photo')->nullable()->after('ktp_number');
            $table->string('selfie_photo')->nullable()->after('ktp_photo');
            $table->string('verification_status')->default('approved')->after('selfie_photo'); // 'pending', 'approved', 'rejected'
        });

        // Tambah kolom penentu lokasi di tabel venues
        Schema::table('venues', function (Blueprint $table) {
            $table->text('google_maps_url')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'ktp_number', 'ktp_photo', 'selfie_photo', 'verification_status']);
        });

        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn('google_maps_url');
        });
    }
};