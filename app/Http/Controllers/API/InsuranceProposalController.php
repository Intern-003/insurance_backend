<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InsuranceProposal;
use App\Models\InsuranceCoverage;
use Carbon\Carbon;
use App\Models\PolicyRenewal;
use App\Models\InsuranceRider;

class InsuranceProposalController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STORE INSURANCE PROPOSAL
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'insurance_plan_id' => 'required|exists:insurance_plans,id',

            'insurance_coverage_id' =>
                'nullable|exists:insurance_coverages,id',

            'category' =>
                'required|in:health,car,life,travel,bike,home,business,investment',

            'full_name' => 'required|string|max:255',

            'mobile' => 'required|string|max:20',

            'date_of_birth' => 'required|date',

            'premium_amount' => 'required|numeric',
        ]);

        /*
        |--------------------------------------------------------------------------
        | GENERATE APPLICATION NUMBER
        |--------------------------------------------------------------------------
        */

        $applicationNumber =
            'APP' . strtoupper(uniqid());

        /*
        |--------------------------------------------------------------------------
        | POLICY DATES
        |--------------------------------------------------------------------------
        */

        $startDate = Carbon::now();

        $endDate = Carbon::now()
            ->addYears($request->tenure ?? 1);

        /*
        |--------------------------------------------------------------------------
        | CREATE PROPOSAL
        |--------------------------------------------------------------------------
        */

        $proposal = InsuranceProposal::create([

            'insurance_plan_id' =>
                $request->insurance_plan_id,

            'insurance_coverage_id' =>
                $request->insurance_coverage_id,

            'category' => $request->category,

            /*
            |--------------------------------------------------------------------------
            | IDS
            |--------------------------------------------------------------------------
            */

            'application_number' =>
                $applicationNumber,

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER DETAILS
            |--------------------------------------------------------------------------
            */

            'full_name' => $request->full_name,

            'email' => $request->email,

            'mobile' => $request->mobile,

            'date_of_birth' =>
                $request->date_of_birth,

            /*
            |--------------------------------------------------------------------------
            | PREMIUM
            |--------------------------------------------------------------------------
            */

            'premium_amount' =>
                $request->premium_amount,

            'tenure' =>
                $request->tenure ?? 1,

            /*
            |--------------------------------------------------------------------------
            | JSON DATA
            |--------------------------------------------------------------------------
            */

            'selected_riders' =>
                $request->selected_riders,

            'customer_details' =>
                $request->customer_details,

            'vehicle_details' =>
                $request->vehicle_details,

            'nominee_details' =>
                $request->nominee_details,

            /*
            |--------------------------------------------------------------------------
            | POLICY DATES
            |--------------------------------------------------------------------------
            */

            'policy_start_date' => $startDate,

            'policy_end_date' => $endDate,

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            'payment_status' => 'pending',

            'policy_status' => 'pending',
        ]);

        return response()->json([

            'status' => true,

            'message' =>
                'Insurance proposal created successfully',

            'proposal' => $proposal,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GET PROPOSAL
    |--------------------------------------------------------------------------
    */

    public function show($application_number)
    {
        $proposal = InsuranceProposal::with([
            'plan',
            'coverage'
        ])
        ->where(
            'application_number',
            $application_number
        )
        ->first();

        if (!$proposal) {

            return response()->json([
                'status' => false,
                'message' => 'Proposal not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'proposal' => $proposal,
        ]);
    }
    /*
|--------------------------------------------------------------------------
| COMPLETE PAYMENT
|--------------------------------------------------------------------------
*/

public function completePayment(Request $request)
{
    $request->validate([

        'application_number' =>
            'required|exists:insurance_proposals,application_number',

        'renewal' =>
            'nullable|boolean',

        'old_policy_id' =>
            'nullable|exists:insurance_proposals,id',

        'coverage_id' =>
            'nullable|exists:insurance_coverages,id',

        'tenure' =>
            'nullable|in:1,2,3',

        'selected_riders' =>
            'nullable|array',
    ]);

    /*
    |--------------------------------------------------------------------------
    | GET PROPOSAL
    |--------------------------------------------------------------------------
    */

    $proposal = InsuranceProposal::where(
        'application_number',
        $request->application_number
    )->first();

    if (!$proposal) {

        return response()->json([

            'status' => false,

            'message' => 'Proposal not found',

        ], 404);
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT ALREADY DONE
    |--------------------------------------------------------------------------
    */

    if (
        $proposal->payment_status === 'paid' &&
        !$request->renewal
    ) {

        return response()->json([

            'status' => false,

            'message' =>
                'Payment already completed',

        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE IDS
    |--------------------------------------------------------------------------
    */

    $policyNumber =
        'POL' . strtoupper(uniqid());

    $transactionId =
        'TXN' . strtoupper(uniqid());

    /*
    |--------------------------------------------------------------------------
    | RENEWAL FLOW
    |--------------------------------------------------------------------------
    */

    if ($request->renewal) {

        /*
        |--------------------------------------------------------------------------
        | GET OLD POLICY
        |--------------------------------------------------------------------------
        */

        $oldPolicy = InsuranceProposal::find(
            $request->old_policy_id
        );

        /*
        |--------------------------------------------------------------------------
        | COVERAGE
        |--------------------------------------------------------------------------
        */

        $coverage = InsuranceCoverage::find(
            $request->coverage_id
                ?? $proposal->insurance_coverage_id
        );

        /*
        |--------------------------------------------------------------------------
        | PREMIUM
        |--------------------------------------------------------------------------
        */

        $premium = $proposal->premium_amount;

        /*
        |--------------------------------------------------------------------------
        | RIDERS
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

                $riderAmount +=
                    $rider->price ?? 0;

                $selectedRiders[] = [

                    'id' =>
                        $rider->id,

                    'title' =>
                        $rider->title,
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FINAL PREMIUM
        |--------------------------------------------------------------------------
        */

        $finalPremium =
            $premium + $riderAmount;

        /*
        |--------------------------------------------------------------------------
        | POLICY DATES
        |--------------------------------------------------------------------------
        */

        $startDate = now();

        if (
            $oldPolicy &&
            $oldPolicy->policy_end_date
        ) {

            $startDate = Carbon::parse(
                $oldPolicy->policy_end_date
            );
        }

        $endDate =
            (clone $startDate)->addYears(
                $proposal->tenure ?? 1
            );

        /*
        |--------------------------------------------------------------------------
        | UPDATE NEW POLICY
        |--------------------------------------------------------------------------
        */

        $proposal->update([

            'policy_number' =>
                $policyNumber,

            'insurance_coverage_id' =>
                $coverage?->id,

            'premium_amount' =>
                $finalPremium,

            'selected_riders' =>
                $selectedRiders,

            'transaction_id' =>
                $transactionId,

            'payment_status' =>
                'paid',

            'policy_status' =>
                'active',

            'payment_date' =>
                now(),

            'policy_start_date' =>
                $startDate,

            'policy_end_date' =>
                $endDate,
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE OLD POLICY
        |--------------------------------------------------------------------------
        */

        if ($oldPolicy) {

            $oldPolicy->update([

                'policy_status' =>
                    'renewed',
            ]);

            /*
            |--------------------------------------------------------------------------
            | STORE RENEWAL HISTORY
            |--------------------------------------------------------------------------
            */

            PolicyRenewal::create([

                'proposal_id' =>
                    $oldPolicy->id,

                'renewed_proposal_id' =>
                    $proposal->id,

                'old_policy_number' =>
                    $oldPolicy->policy_number,

                'new_policy_number' =>
                    $proposal->policy_number,

                'renewal_date' =>
                    now(),

                'old_expiry_date' =>
                    $oldPolicy->policy_end_date,

                'new_expiry_date' =>
                    $proposal->policy_end_date,

                'premium_amount' =>
                    $proposal->premium_amount,

                'status' =>
                    'completed',
            ]);
        }

        $proposal->load([
            'plan',
            'coverage',
        ]);

        return response()->json([

            'status' => true,

            'message' =>
                'Policy renewed successfully',

            'proposal' =>
                $proposal,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | NEW POLICY FLOW
    |--------------------------------------------------------------------------
    */

    $proposal->update([

        'policy_number' =>
            $policyNumber,

        'transaction_id' =>
            $transactionId,

        'payment_status' =>
            'paid',

        'policy_status' =>
            'active',

        'payment_date' =>
            now(),
    ]);

    $proposal->load([
        'plan',
        'coverage',
    ]);

    return response()->json([

        'status' => true,

        'message' =>
            'Payment completed successfully',

        'proposal' =>
            $proposal,
    ]);
}
/*
|--------------------------------------------------------------------------
| VERIFY RENEWAL
|--------------------------------------------------------------------------
*/

public function verifyRenewal(Request $request)

{
    // dd($request->all());
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

            'id' => $policy->id,

            'plan_name' =>
                $policy->plan?->plan_name,

            'slug' =>
                $policy->plan?->slug,

            'category' =>
                $policy->category,

            'customer_name' =>
                $policy->full_name,

            'application_number' =>
                $policy->application_number,

            'policy_number' =>
                $policy->policy_number,

            'premium_amount' =>
                $policy->premium_amount,

            'formatted_premium' =>
                '₹' . number_format($policy->premium_amount),

            'tenure' =>
                $policy->tenure,

            'policy_status' =>
                $policy->policy_status,

            'payment_status' =>
                $policy->payment_status,

            'policy_start_date' =>
                $policy->policy_start_date,

            'policy_end_date' =>
                $policy->policy_end_date,

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
            ],

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

/*
|--------------------------------------------------------------------------
| INVOICE API
|--------------------------------------------------------------------------
*/

public function invoice($application_number)
{
    /*
    |--------------------------------------------------------------------------
    | GET PROPOSAL
    |--------------------------------------------------------------------------
    */

    $proposal = InsuranceProposal::with([
        'plan',
        'coverage',
    ])
    ->where(
        'application_number',
        $application_number
    )
    ->where('payment_status', 'paid')
    ->first();

    /*
    |--------------------------------------------------------------------------
    | NOT FOUND
    |--------------------------------------------------------------------------
    */

    if (!$proposal) {

        return response()->json([
            'status' => false,
            'message' => 'Invoice not found',
        ], 404);
    }

    /*
    |--------------------------------------------------------------------------
    | CALCULATIONS
    |--------------------------------------------------------------------------
    */

    $baseAmount =
        (float) $proposal->premium_amount;

    $gstAmount =
        round(($baseAmount * 18) / 100, 2);

    $totalAmount =
        $baseAmount + $gstAmount;

    return response()->json([

        'status' => true,

        'message' =>
            'Invoice fetched successfully',

        /*
        |--------------------------------------------------------------------------
        | INVOICE DETAILS
        |--------------------------------------------------------------------------
        */

        'invoice' => [

            'invoice_number' =>
                'INV-' . $proposal->id,

            'application_number' =>
                $proposal->application_number,

            'policy_number' =>
                $proposal->policy_number,

            'transaction_id' =>
                $proposal->transaction_id,

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER
            |--------------------------------------------------------------------------
            */

            'customer_name' =>
                $proposal->full_name,

            'email' =>
                $proposal->email,

            'mobile' =>
                $proposal->mobile,

            /*
            |--------------------------------------------------------------------------
            | PLAN DETAILS
            |--------------------------------------------------------------------------
            */

            'insurance_category' =>
                $proposal->category,

            'plan_name' =>
                optional($proposal->plan)->plan_name,

            'company_name' =>
                optional($proposal->plan)->company_name,

            /*
            |--------------------------------------------------------------------------
            | COVERAGE
            |--------------------------------------------------------------------------
            */

            'coverage' =>
                optional($proposal->coverage)
                    ->coverage_name,

            /*
            |--------------------------------------------------------------------------
            | PREMIUM
            |--------------------------------------------------------------------------
            */

            'base_premium' =>
                $baseAmount,

            'gst_amount' =>
                $gstAmount,

            'total_amount' =>
                $totalAmount,

            /*
            |--------------------------------------------------------------------------
            | POLICY DATES
            |--------------------------------------------------------------------------
            */

            'policy_start_date' =>
                $proposal->policy_start_date,

            'policy_end_date' =>
                $proposal->policy_end_date,

            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */

            'payment_date' =>
                $proposal->payment_date,

            'payment_status' =>
                $proposal->payment_status,

            /*
            |--------------------------------------------------------------------------
            | RIDERS
            |--------------------------------------------------------------------------
            */

            'selected_riders' =>
                $proposal->selected_riders,
        ],
    ]);
} 


}