<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('monthly'); // 'weekly', 'monthly'
            $table->integer('month')->nullable(); // Month 1-12
            $table->integer('year'); // Year
            $table->integer('weekly')->nullable(); // Week number
            $table->decimal('total', 15, 2); // Actual sales
            $table->decimal('st', 15, 4)->nullable(); // Smoothed statistic
            $table->decimal('sst', 15, 4)->nullable(); // Double smoothed statistic
            $table->decimal('at', 15, 4)->nullable(); // Level
            $table->decimal('bt', 15, 4)->nullable(); // Trend
            $table->decimal('forecast', 15, 4)->nullable(); // Forecast value
            $table->decimal('pe', 15, 4)->nullable(); // Percentage Error
            $table->decimal('selisih', 15, 4)->nullable(); // Difference (Actual - Forecast)
            $table->string('evaluasi')->nullable(); // Evaluation metric description or value
            $table->decimal('actual_prev', 15, 4)->nullable(); // Previous actual
            $table->decimal('alpha', 5, 4)->default(0.1); // Smoothing parameter
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecasts');
    }
};
