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
        Schema::table('fields', function (Blueprint $table) {
            // Kita tambahkan 2 kolom yang dibutuhkan logic kita
            $table->integer('break_quota_minutes')->default(120)->after('name'); 
            $table->timestamp('last_quota_reset')->nullable()->after('break_quota_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropColumn(['break_quota_minutes', 'last_quota_reset']);
        });
    }
};
