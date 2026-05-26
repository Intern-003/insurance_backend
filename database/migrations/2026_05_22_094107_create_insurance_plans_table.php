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
       Schema::create('insurance_plans', function (Blueprint $table) {
    $table->id();

    // STATIC CATEGORY
    $table->enum('category', [
        'health',
        'car',
        'life',
        'travel',
        'bike',
        'home',
        'business',
        'investment'
    ]);

    $table->string('plan_name');

    $table->string('slug')->unique();

    $table->string('company_name');

    $table->string('logo')->nullable();

    $table->text('short_description')->nullable();

    $table->longText('description')->nullable();

    $table->decimal('starting_price', 10, 2)->default(0);

    $table->integer('cashless_hospitals')->nullable();

    $table->string('claim_ratio')->nullable();

    $table->boolean('is_featured')->default(false);

    $table->boolean('is_popular')->default(false);

    $table->boolean('status')->default(true);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_plans');
    }
};
