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
Schema::create('insurance_coverages', function (Blueprint $table) {
    $table->id();

    $table->foreignId('insurance_plan_id')
        ->constrained()
        ->onDelete('cascade');

    $table->string('coverage_name');

    $table->decimal('coverage_amount', 12, 2);

    // 1 YEAR PRICE
    $table->decimal('one_year_price', 10, 2);

    // 2 YEAR PRICE
    $table->decimal('two_year_price', 10, 2)->nullable();

    // 3 YEAR PRICE
    $table->decimal('three_year_price', 10, 2)->nullable();

    $table->boolean('is_recommended')->default(false);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_coverages');
    }
};
