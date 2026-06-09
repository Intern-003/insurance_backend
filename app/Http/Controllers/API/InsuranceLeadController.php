<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InsuranceLead;

class InsuranceLeadController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STORE INSURANCE LEAD
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'category' =>
                'required|in:health,car,life,travel,bike,home,business,investment',
        ]);

        $lead = InsuranceLead::create([

            'category' =>
                $request->category,

            'full_name' =>
                $request->full_name,

            'mobile' =>
                $request->mobile,

            'email' =>
                $request->email,

            'date_of_birth' =>
                $request->date_of_birth,

            'vehicle_number' =>
                $request->vehicle_number,

            'destination' =>
                $request->destination,

            'travel_start_date' =>
                $request->travel_start_date,

            'travel_end_date' =>
                $request->travel_end_date,

            'extra_details' =>
                $request->extra_details,
        ]);

        return response()->json([

            'status' => true,

            'message' =>
                'Insurance lead stored successfully',

            'lead' => $lead,
        ]);
    }
}