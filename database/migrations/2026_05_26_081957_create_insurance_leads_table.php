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
        Schema::create('insurance_leads', function (Blueprint $table) {

            $table->id();

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
            | BASIC DETAILS
            |--------------------------------------------------------------------------
            */

            $table->string('full_name')->nullable();

            $table->string('mobile')->nullable();

            $table->string('email')->nullable();

            $table->date('date_of_birth')->nullable();

            /*
            |--------------------------------------------------------------------------
            | VEHICLE DETAILS
            |--------------------------------------------------------------------------
            */

            $table->string('vehicle_number')->nullable();

            /*
            |--------------------------------------------------------------------------
            | TRAVEL DETAILS
            |--------------------------------------------------------------------------
            */

            $table->string('destination')->nullable();

            $table->date('travel_start_date')->nullable();

            $table->date('travel_end_date')->nullable();

            /*
            |--------------------------------------------------------------------------
            | EXTRA DETAILS
            |--------------------------------------------------------------------------
            */

            $table->json('extra_details')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_leads');
    }
};