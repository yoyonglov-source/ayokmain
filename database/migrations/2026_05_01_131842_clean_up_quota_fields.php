<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fields', function (Blueprint $table) {
            // Menghapus kolom sisa jatah menit dan reset terakhirnya
            $table->dropColumn(['break_quota_minutes', 'last_quota_reset']);
        });
    }

    public function down()
    {
        Schema::table('fields', function (Blueprint $table) {
            // Jika rollback, kolom dikembalikan (optional)
            $table->integer('break_quota_minutes')->default(120);
            $table->timestamp('last_quota_reset')->nullable();
        });
    }
};