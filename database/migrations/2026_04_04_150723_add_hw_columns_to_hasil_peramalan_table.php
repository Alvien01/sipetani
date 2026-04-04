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
        Schema::table('hasil_peramalan', function (Blueprint $table) {
            $table->double('gamma', 5, 2)->default(0.00)->after('beta');
            $table->integer('seasonal_length')->default(0)->after('gamma');
            $table->double('it', 10, 2)->nullable()->after('bt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_peramalan', function (Blueprint $table) {
            $table->dropColumn(['gamma', 'seasonal_length', 'it']);
        });
    }
};
