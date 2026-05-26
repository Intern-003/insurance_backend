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
Schema::create('insurance_riders', function (Blueprint $table) {
    $table->id();

    $table->foreignId('insurance_plan_id')
        ->constrained()
        ->onDelete('cascade');

    $table->string('title');

    $table->text('description')->nullable();

    $table->decimal('price', 10, 2)->default(0);

    $table->boolean('status')->default(true);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_riders');
    }
};
