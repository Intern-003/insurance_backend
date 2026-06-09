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
        Schema::create('insurance_plan_selections', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | RELATIONS
            |--------------------------------------------------------------------------
            */

            $table->foreignId('insurance_plan_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('insurance_coverage_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

            /*
            |--------------------------------------------------------------------------
            | CATEGORY
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | PREMIUM
            |--------------------------------------------------------------------------
            */

            $table->decimal('premium_amount', 10, 2)
                ->default(0);

            $table->integer('tenure')
                ->default(1);

            /*
            |--------------------------------------------------------------------------
            | RIDERS
            |--------------------------------------------------------------------------
            */

            $table->json('selected_riders')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | EXTRA DATA
            |--------------------------------------------------------------------------
            */

            $table->json('extra_details')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_plan_selections');
    }
};