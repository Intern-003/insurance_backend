<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceProposal extends Model
{
    use HasFactory;

    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | PLAN DETAILS
        |--------------------------------------------------------------------------
        */

        'insurance_plan_id',
        'insurance_coverage_id',
        'category',

        /*
        |--------------------------------------------------------------------------
        | UNIQUE IDS
        |--------------------------------------------------------------------------
        */

        'application_number',
        'policy_number',

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER DETAILS
        |--------------------------------------------------------------------------
        */

        'full_name',
        'email',
        'mobile',
        'date_of_birth',

        /*
        |--------------------------------------------------------------------------
        | PREMIUM DETAILS
        |--------------------------------------------------------------------------
        */

        'premium_amount',
        'tenure',

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        'payment_status',
        'policy_status',

        /*
        |--------------------------------------------------------------------------
        | JSON DATA
        |--------------------------------------------------------------------------
        */

        'selected_riders',
        'customer_details',
        'vehicle_details',
        'nominee_details',

        /*
        |--------------------------------------------------------------------------
        | PAYMENT DETAILS
        |--------------------------------------------------------------------------
        */

        'transaction_id',
        'payment_date',

        /*
        |--------------------------------------------------------------------------
        | POLICY DATES
        |--------------------------------------------------------------------------
        */

        'policy_start_date',
        'policy_end_date',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'selected_riders' => 'array',

        'customer_details' => 'array',

        'vehicle_details' => 'array',

        'nominee_details' => 'array',

        'payment_date' => 'datetime',

        'policy_start_date' => 'date',

        'policy_end_date' => 'date',

        'premium_amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | APPENDS
    |--------------------------------------------------------------------------
    */

    protected $appends = [
        'formatted_premium',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

public function plan()
{
    return $this->belongsTo(
        InsurancePlan::class,
        'insurance_plan_id',
        'id'
    );
}

    public function coverage()
    {
        return $this->belongsTo(
            InsuranceCoverage::class,
            'insurance_coverage_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getFormattedPremiumAttribute()
    {
        return '₹' . number_format($this->premium_amount, 0);
    }

    public function renewals()
{
    return $this->hasMany(
        PolicyRenewal::class,
        'proposal_id'
    );
}
}