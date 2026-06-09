<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsurancePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'plan_name',
        'slug',
        'company_name',
        'logo',
        'short_description',
        'description',
        'starting_price',
        'cashless_hospitals',
        'claim_ratio',
        'is_featured',
        'is_popular',
        'status',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'status' => 'boolean',
        'starting_price' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | APPENDS
    |--------------------------------------------------------------------------
    */

    protected $appends = [
        'logo_url',
        'formatted_starting_price',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // public function coverages()
    // {
    //     return $this->hasMany(InsuranceCoverage::class);
    // }

    public function features()
    {
        return $this->hasMany(InsuranceFeature::class);
    }

    public function riders()
    {
        return $this->hasMany(InsuranceRider::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return null;
        }

        return asset('storage/' . $this->logo);
    }

    public function getFormattedStartingPriceAttribute()
    {
        return '₹' . number_format($this->starting_price, 0);
    }
    public function coverages()
{
    return $this->hasMany(
        InsuranceCoverage::class,
        'insurance_plan_id'
    );
}
}