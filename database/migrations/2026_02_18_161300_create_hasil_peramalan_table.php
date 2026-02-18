<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_peramalan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produk')->constrained('products')->cascadeOnDelete();
            $table->string('periode', 20);
            $table->enum('tipe_periode', ['bulanan', 'mingguan']);
            $table->integer('aktual')->nullable();
            $table->double('st', 10, 2);
            $table->double('bt', 10, 2);
            $table->double('forecast', 10, 2);
            $table->double('alpha', 5, 2);
            $table->double('beta', 5, 2)->default(0.00);
            $table->double('pe', 10, 2)->nullable();
            $table->double('mape', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_peramalan');
    }
};
