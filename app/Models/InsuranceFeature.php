<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'insurance_plan_id',
        'feature',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function plan()
    {
        return $this->belongsTo(InsurancePlan::class);
    }
}