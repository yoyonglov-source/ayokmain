<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('operating_hours', function (Blueprint $table) {
            // Kita tambahkan rentang jam untuk harga Peak
            $table->time('peak_start')->nullable()->default('17:00')->after('close_time');
            $table->time('peak_end')->nullable()->default('22:00')->after('peak_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operating_hours', function (Blueprint $table) {
            //
        });
    }
};
