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
            Schema::create('venues', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
                $table->string('name');
                $table->string('slug');
                $table->string('category');
                $table->text('address');
                $table->string('city');
                $table->string('phone_number');
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->boolean('is_active')->default(true);

                // --- PERBAIKAN: Hapus ->after() ---
                $table->string('fee_mode')->default('addon'); 
                $table->string('pg_fee_bearer')->default('customer');
                $table->decimal('platform_fee', 10, 2)->default(5000);
                // ---------------------------------

                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
         $table->dropColumn(['fee_mode', 'pg_fee_bearer', 'platform_fee']);
    });
    }
};
