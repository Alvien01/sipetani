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
        Schema::create('stock_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produk')->constrained('products')->cascadeOnDelete();
            $table->string('periode', 20);
            $table->enum('tipe_periode', ['bulanan', 'mingguan']);
            $table->integer('current_stock')->default(0);
            $table->double('forecast_qty', 10, 2)->default(0);
            $table->integer('safety_stock')->default(0);
            $table->integer('recommended_qty')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_recommendations');
    }
};
