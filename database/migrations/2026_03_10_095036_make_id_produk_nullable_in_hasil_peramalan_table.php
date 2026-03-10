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
            $table->foreignId('id_produk')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_peramalan', function (Blueprint $table) {
            $table->foreignId('id_produk')->nullable(false)->change();
        });
    }
};
