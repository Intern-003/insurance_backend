<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsurancePlanSelection extends Model
{
    use HasFactory;

    protected $fillable = [

        'insurance_plan_id',

        'insurance_coverage_id',

        'category',

        'premium_amount',

        'tenure',

        'selected_riders',

        'extra_details',
    ];

    protected $casts = [

        'selected_riders' => 'array',

        'extra_details' => 'array',

        'premium_amount' => 'decimal:2',
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
            'insurance_plan_id'
        );
    }

    public function coverage()
    {
        return $this->belongsTo(
            InsuranceCoverage::class,
            'insurance_coverage_id'
        );
    }
}