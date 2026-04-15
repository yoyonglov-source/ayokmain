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
        // Gunakan create karena tabelnya belum ada sama sekali
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            
            // Relasi (Pastikan foreign key ini sesuai dengan kebutuhan Anda)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('venue_id')->constrained()->onDelete('cascade');
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            
            // Waktu Booking
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');

            // --- BREAKDOWN PEMBAYARAN ---
            $table->decimal('base_price', 12, 2); 
            $table->decimal('app_fee', 12, 2)->default(0);
            $table->string('app_fee_bearer'); // 'customer' atau 'owner'
            
            $table->decimal('pg_fee', 12, 2)->default(0);
            $table->string('pg_fee_bearer'); // 'customer' atau 'owner'
            $table->string('payment_method')->nullable();
            
            $table->decimal('total_amount', 12, 2); 
            $table->decimal('net_profit_owner', 12, 2);
            
            $table->string('status')->default('pending'); // pending, success, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }

};
