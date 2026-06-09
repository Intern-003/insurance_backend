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
        Schema::create('insurance_proposals', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | PLAN DETAILS
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
            | UNIQUE IDS
            |--------------------------------------------------------------------------
            */

            $table->string('application_number')->unique();

            $table->string('policy_number')
                ->nullable()
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER DETAILS
            |--------------------------------------------------------------------------
            */

            $table->string('full_name');

            $table->string('email')->nullable();

            $table->string('mobile');

            $table->date('date_of_birth');

            /*
            |--------------------------------------------------------------------------
            | PREMIUM DETAILS
            |--------------------------------------------------------------------------
            */

            $table->decimal('premium_amount', 10, 2)->default(0);

            $table->integer('tenure')->default(1);

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $table->enum('payment_status', [
                'pending',
                'paid',
                'failed'
            ])->default('pending');

            $table->enum('policy_status', [
                'pending',
                'active',
                'expired',
                'cancelled'
            ])->default('pending');

            /*
            |--------------------------------------------------------------------------
            | JSON DATA
            |--------------------------------------------------------------------------
            */

            // RIDERS
            $table->json('selected_riders')->nullable();

            // CUSTOMER EXTRA DETAILS
            $table->json('customer_details')->nullable();

            // VEHICLE DETAILS
            $table->json('vehicle_details')->nullable();

            // NOMINEE DETAILS
            $table->json('nominee_details')->nullable();

            /*
            |--------------------------------------------------------------------------
            | PAYMENT DETAILS
            |--------------------------------------------------------------------------
            */

            $table->string('transaction_id')->nullable();

            $table->timestamp('payment_date')->nullable();

            /*
            |--------------------------------------------------------------------------
            | POLICY DATES
            |--------------------------------------------------------------------------
            */

            $table->date('policy_start_date')->nullable();

            $table->date('policy_end_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_proposals');
    }
};