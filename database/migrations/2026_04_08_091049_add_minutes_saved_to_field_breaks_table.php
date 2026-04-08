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
        Schema::table('field_breaks', function (Blueprint $table) {
            $table->integer('minutes_saved')->default(0)->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_breaks', function (Blueprint $table) {
            //
        });
    }
};
