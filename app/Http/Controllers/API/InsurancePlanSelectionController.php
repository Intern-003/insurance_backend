<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InsurancePlanSelection;

class InsurancePlanSelectionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STORE PLAN SELECTION
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'insurance_plan_id' =>
                'required|exists:insurance_plans,id',

            'insurance_coverage_id' =>
                'nullable|exists:insurance_coverages,id',

            'category' =>
                'required|in:health,car,life,travel,bike,home,business,investment',

            'premium_amount' =>
                'required|numeric',

            'tenure' =>
                'required|integer|min:1|max:3',
        ]);

        $selection = InsurancePlanSelection::create([

            'insurance_plan_id' =>
                $request->insurance_plan_id,

            'insurance_coverage_id' =>
                $request->insurance_coverage_id,

            'category' =>
                $request->category,

            'premium_amount' =>
                $request->premium_amount,

            'tenure' =>
                $request->tenure,

            'selected_riders' =>
                $request->selected_riders,

            'extra_details' =>
                $request->extra_details,
        ]);

        return response()->json([

            'status' => true,

            'message' =>
                'Plan selection stored successfully',

            'selection' => $selection,
        ]);
    }
}