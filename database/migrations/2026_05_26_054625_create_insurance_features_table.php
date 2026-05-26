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
Schema::create('insurance_features', function (Blueprint $table) {
    $table->id();

    $table->foreignId('insurance_plan_id')
        ->constrained()
        ->onDelete('cascade');

    $table->string('feature');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_features');
    }
};
