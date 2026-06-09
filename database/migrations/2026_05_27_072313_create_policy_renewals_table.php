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
        Schema::create('policy_renewals', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | OLD POLICY
            |--------------------------------------------------------------------------
            */

            $table->foreignId('proposal_id')
                ->constrained('insurance_proposals')
                ->onDelete('cascade');

            /*
            |--------------------------------------------------------------------------
            | NEW RENEWED POLICY
            |--------------------------------------------------------------------------
            */

            $table->foreignId('renewed_proposal_id')
                ->nullable()
                ->constrained('insurance_proposals')
                ->onDelete('cascade');

            /*
            |--------------------------------------------------------------------------
            | POLICY NUMBERS
            |--------------------------------------------------------------------------
            */

            $table->string('old_policy_number');

            $table->string('new_policy_number');

            /*
            |--------------------------------------------------------------------------
            | DATES
            |--------------------------------------------------------------------------
            */

            $table->date('renewal_date');

            $table->date('old_expiry_date');

            $table->date('new_expiry_date');

            /*
            |--------------------------------------------------------------------------
            | RENEWAL DETAILS
            |--------------------------------------------------------------------------
            */

            $table->integer('renewal_tenure')
                ->nullable();

            $table->decimal(
                'premium_amount',
                12,
                2
            );

            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */

            $table->string('transaction_id')
                ->nullable();

            $table->string('payment_status')
                ->default('paid');

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $table->string('status')
                ->default('completed');

            /*
            |--------------------------------------------------------------------------
            | RIDERS
            |--------------------------------------------------------------------------
            */

            $table->json('selected_riders')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_renewals');
    }
};