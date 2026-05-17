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
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();
            // Menghubungkan detail ini ke tabel utama bookings
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            
            // Memindahkan pencatatan lapangan dan jam ke sini agar bisa multi-slot
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time');
            
            // Mencatat harga per slot jam ini (berjaga-jaga jika ada harga reguler vs peak)
            $table->decimal('price', 12, 2); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_details');
    }
};
