<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceCoverage extends Model
{
    use HasFactory;

    protected $table = 'insurance_coverages';

    protected $fillable = [
        'insurance_plan_id',
        'coverage_name',
        'coverage_amount',
        'one_year_price',
        'two_year_price',
        'three_year_price',
        'is_recommended',
    ];

    protected $casts = [
        'coverage_amount'   => 'decimal:2',
        'one_year_price'    => 'decimal:2',
        'two_year_price'    => 'decimal:2',
        'three_year_price'  => 'decimal:2',
        'is_recommended'    => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function plan()
    {
        return $this->belongsTo(InsurancePlan::class, 'insurance_plan_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getFormattedCoverageAmountAttribute()
    {
        if ($this->coverage_amount >= 10000000) {
            return '₹' . ($this->coverage_amount / 10000000) . ' Cr';
        }

        if ($this->coverage_amount >= 100000) {
            return '₹' . ($this->coverage_amount / 100000) . ' Lakh';
        }

        return '₹' . number_format($this->coverage_amount);
    }

    public function getFormattedOneYearPriceAttribute()
    {
        return '₹' . number_format($this->one_year_price, 0);
    }

    public function getFormattedTwoYearPriceAttribute()
    {
        return $this->two_year_price
            ? '₹' . number_format($this->two_year_price, 0)
            : null;
    }

    public function getFormattedThreeYearPriceAttribute()
    {
        return $this->three_year_price
            ? '₹' . number_format($this->three_year_price, 0)
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeRecommended($query)
    {
        return $query->where('is_recommended', true);
    }
}