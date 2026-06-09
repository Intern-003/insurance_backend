<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("

            ALTER TABLE insurance_proposals

            MODIFY policy_status

            ENUM(
                'pending',
                'active',
                'expired',
                'renewed',
                'cancelled'
            )

            DEFAULT 'pending'

        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("

            ALTER TABLE insurance_proposals

            MODIFY policy_status

            ENUM(
                'pending',
                'active',
                'expired'
            )

            DEFAULT 'pending'

        ");
    }
};