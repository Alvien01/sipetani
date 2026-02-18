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
        Schema::create('personels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rank')->nullable(); // Pangkat
            $table->string('nrp')->unique();
            $table->string('position')->nullable(); // Jabatan
            $table->string('status')->default('Tersedia'); // Tersedia, Siaga, Terkonfirmasi
            $table->string('fcm_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personels');
    }
};
