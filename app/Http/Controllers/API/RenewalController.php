<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InsuranceProposal;
use App\Models\InsuranceCoverage;
use App\Models\InsuranceRider;
use Carbon\Carbon;


class RenewalController extends Controller
{
public function verify(Request $request)
{
    $request->validate([

        'date_of_birth' => 'required|date',

        'application_number' =>
            'nullable|string',

        'policy_number' =>
            'nullable|string',
    ]);

    /*
    |--------------------------------------------------------------------------
    | CHECK INPUT
    |--------------------------------------------------------------------------
    */

    if (
        !$request->application_number &&
        !$request->policy_number
    ) {

        return response()->json([

            'status' => false,

            'message' =>
                'Application number or policy number is required',

        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | FETCH POLICIES
    |--------------------------------------------------------------------------
    */

    $policies = InsuranceProposal::with([

        'plan',
        'coverage',

    ])

    ->whereDate(
        'date_of_birth',
        $request->date_of_birth
    )

    ->where(function ($query) use ($request) {

        if ($request->application_number) {

            $query->where(
                'application_number',
                $request->application_number
            );
        }

        if ($request->policy_number) {

            $query->orWhere(
                'policy_number',
                $request->policy_number
            );
        }
    })

    ->where('payment_status', 'paid')

    ->where('policy_status', 'active')

    ->latest()

    ->get();

    /*
    |--------------------------------------------------------------------------
    | NO POLICY FOUND
    |--------------------------------------------------------------------------
    */

    if ($policies->isEmpty()) {

        return response()->json([

            'status' => false,

            'message' =>
                'No active policy found',

        ], 404);
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT RESPONSE
    |--------------------------------------------------------------------------
    */

    $formattedPolicies = $policies->map(function ($policy) {

        return [

            /*
            |--------------------------------------------------------------------------
            | BASIC DETAILS
            |--------------------------------------------------------------------------
            */

            'id' => $policy->id,

            'category' =>
                $policy->category,

            'application_number' =>
                $policy->application_number,

            'policy_number' =>
                $policy->policy_number,

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER DETAILS
            |--------------------------------------------------------------------------
            */

            'customer_name' =>
                $policy->full_name,

            'full_name' =>
                $policy->full_name,

            'mobile' =>
                $policy->mobile,

            'email' =>
                $policy->email,

            'date_of_birth' =>
                $policy->date_of_birth,

            /*
            |--------------------------------------------------------------------------
            | PREMIUM DETAILS
            |--------------------------------------------------------------------------
            */

            'premium_amount' =>
                $policy->premium_amount,

            'formatted_premium' =>
                '₹' . number_format($policy->premium_amount),

            'tenure' =>
                $policy->tenure,

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            'payment_status' =>
                $policy->payment_status,

            'policy_status' =>
                $policy->policy_status,

            /*
            |--------------------------------------------------------------------------
            | POLICY DATES
            |--------------------------------------------------------------------------
            */

            'policy_start_date' =>
                $policy->policy_start_date,

            'policy_end_date' =>
                $policy->policy_end_date,

            /*
            |--------------------------------------------------------------------------
            | COMPLETE JSON DATA
            |--------------------------------------------------------------------------
            */

            'customer_details' =>
                $policy->customer_details,

            'vehicle_details' =>
                $policy->vehicle_details,

            'nominee_details' =>
                $policy->nominee_details,

            'selected_riders' =>
                $policy->selected_riders,

            /*
            |--------------------------------------------------------------------------
            | PLAN
            |--------------------------------------------------------------------------
            */

            'plan' => [

                'id' =>
                    $policy->plan?->id,

                'slug' =>
                    $policy->plan?->slug,

                'plan_name' =>
                    $policy->plan?->plan_name,

                'company_name' =>
                    $policy->plan?->company_name,

                'logo' =>
                    $policy->plan?->logo,

                'logo_url' =>
                    $policy->plan?->logo_url,

                'starting_price' =>
                    $policy->plan?->starting_price,

                'cashless_hospitals' =>
                    $policy->plan?->cashless_hospitals,

                'claim_ratio' =>
                    $policy->plan?->claim_ratio,
            ],

            /*
            |--------------------------------------------------------------------------
            | COVERAGE
            |--------------------------------------------------------------------------
            */

            'coverage' => [

                'id' =>
                    $policy->coverage?->id,

                'coverage_name' =>
                    $policy->coverage?->coverage_name,

                'coverage_amount' =>
                    $policy->coverage?->coverage_amount,

                'one_year_price' =>
                    $policy->coverage?->one_year_price,

                'two_year_price' =>
                    $policy->coverage?->two_year_price,

                'three_year_price' =>
                    $policy->coverage?->three_year_price,
            ],
        ];
    });

    /*
    |--------------------------------------------------------------------------
    | SUCCESS RESPONSE
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'status' => true,

        'message' =>
            'Policies fetched successfully',

        'total_policies' =>
            $formattedPolicies->count(),

        'policies' =>
            $formattedPolicies,
    ]);
}

public function renewalDetails($proposalId)
{
    $proposal = InsuranceProposal::with([

        'plan.features',
        'plan.riders',
        'plan.coverages',

    ])->find($proposalId);

    if (!$proposal) {

        return response()->json([

            'status' => false,
            'message' => 'Policy not found'

        ], 404);
    }

    return response()->json([

        'status' => true,

        'message' => 'Renewal details fetched successfully',

        'renewal_data' => [

            'proposal_id' => $proposal->id,

            'category' => $proposal->category,

            'policy_number' => $proposal->policy_number,

            'application_number' => $proposal->application_number,

            'customer_name' => $proposal->full_name,

            'email' => $proposal->email,

            'mobile' => $proposal->mobile,

            'date_of_birth' => $proposal->date_of_birth,

            'current_plan' => $proposal->plan,

            'current_coverage_id' => $proposal->insurance_coverage_id,

            'available_coverages' => $proposal->plan->coverages,

            'available_riders' => $proposal->plan->riders,

            'selected_riders' => $proposal->selected_riders,

            'current_premium' => $proposal->premium_amount,

            'current_tenure' => $proposal->tenure,

            'policy_start_date' => $proposal->policy_start_date,

            'policy_end_date' => $proposal->policy_end_date,

            'days_left' => now()->diffInDays(
                $proposal->policy_end_date,
                false
            ),

            'policy_status' => $proposal->policy_status,

            'payment_status' => $proposal->payment_status,
        ]
    ]);
}

public function updateRenewal(Request $request)
{
    $request->validate([

        'proposal_id' => 'required|exists:insurance_proposals,id',

        'coverage_id' => 'required|exists:insurance_coverages,id',

        'tenure' => 'required|in:1,2,3',

        'selected_riders' => 'nullable|array',
    ]);

    $proposal = InsuranceProposal::with([
        'plan',
        'coverage',
    ])->find($request->proposal_id);

    if (!$proposal) {

        return response()->json([

            'status' => false,
            'message' => 'Proposal not found'

        ], 404);
    }

    /*
    |--------------------------------------------------------------------------
    | GET COVERAGE
    |--------------------------------------------------------------------------
    */

    $coverage = InsuranceCoverage::find($request->coverage_id);

    /*
    |--------------------------------------------------------------------------
    | CALCULATE BASE PREMIUM
    |--------------------------------------------------------------------------
    */

    $premium = match ((int) $request->tenure) {

        1 => $coverage->one_year_price,
        2 => $coverage->two_year_price,
        3 => $coverage->three_year_price,

        default => $coverage->one_year_price
    };

    /*
    |--------------------------------------------------------------------------
    | ADD RIDER COST
    |--------------------------------------------------------------------------
    */

    $selectedRiders = [];

    $riderAmount = 0;

    if ($request->selected_riders) {

        $riders = InsuranceRider::whereIn(
            'id',
            $request->selected_riders
        )->get();

        foreach ($riders as $rider) {

            $riderAmount += $rider->price ?? 0;

            $selectedRiders[] = [

                'id' => $rider->id,
                'title' => $rider->title,
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FINAL PREMIUM
    |--------------------------------------------------------------------------
    */

    $finalPremium = $premium + $riderAmount;

    /*
    |--------------------------------------------------------------------------
    | RETURN RESPONSE
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'status' => true,

        'message' => 'Renewal updated successfully',

        'checkout_data' => [

            'proposal_id' => $proposal->id,

            'plan_name' => $proposal->plan->plan_name,

            'category' => $proposal->category,

            'customer_name' => $proposal->full_name,

            'policy_number' => $proposal->policy_number,

            'application_number' => $proposal->application_number,

            'selected_coverage' => [

                'coverage_id' => $coverage->id,

                'coverage_name' => $coverage->coverage_name,

                'coverage_amount' => $coverage->coverage_amount,
            ],

            'selected_riders' => $selectedRiders,

            'tenure' => (int) $request->tenure,

            'base_premium' => $premium,

            'rider_charges' => $riderAmount,

            'final_premium' => $finalPremium,

            'formatted_final_premium' => '₹' . number_format($finalPremium, 0),

            'policy_start_date' => now()->format('Y-m-d'),

            'policy_end_date' => now()
                ->addYears($request->tenure)
                ->format('Y-m-d'),
        ]
    ]);
} 

}