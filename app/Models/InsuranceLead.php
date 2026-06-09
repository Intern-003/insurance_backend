<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceLead extends Model
{
    use HasFactory;

    protected $fillable = [

        'category',

        'full_name',

        'mobile',

        'email',

        'date_of_birth',

        'vehicle_number',

        'destination',

        'travel_start_date',

        'travel_end_date',

        'extra_details',
    ];

    protected $casts = [

        'extra_details' => 'array',

        'date_of_birth' => 'date',

        'travel_start_date' => 'date',

        'travel_end_date' => 'date',
    ];
}