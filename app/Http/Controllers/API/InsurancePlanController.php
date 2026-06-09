<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InsurancePlan;
use Illuminate\Http\Request;
use App\Models\InsuranceCoverage;
use App\Models\InsuranceFeature;
use App\Models\InsuranceRider;

class InsurancePlanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | VALID CATEGORIES
    |--------------------------------------------------------------------------
    */

    private $validCategories = [
        'health',
        'car',
        'life',
        'travel',
        'bike',
        'home',
        'business',
        'investment',
    ];

    /*
    |--------------------------------------------------------------------------
    | GET ALL PLANS BY CATEGORY
    |--------------------------------------------------------------------------
    */

    public function index($category)
    {
        // VALIDATE CATEGORY
        if (!in_array($category, $this->validCategories)) {

            return response()->json([
                'status' => false,
                'message' => 'Invalid insurance category',
            ], 422);
        }

        // GET PLANS
        $plans = InsurancePlan::with([
            'coverages',
            'features',
            'riders',
        ])
        ->where('category', $category)
        ->where('status', true)
        ->latest()
        ->get();

        return response()->json([
            'status' => true,

            'category' => $category,

            'total_plans' => $plans->count(),

            'plans' => $plans,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GET SINGLE PLAN
    |--------------------------------------------------------------------------
    */

public function show($slug)
{
    /*
    |--------------------------------------------------------------------------
    | FETCH PLAN USING SLUG
    |--------------------------------------------------------------------------
    */

    $plan = InsurancePlan::with([
        'coverages',
        'features',
        'riders',
    ])

    ->where('slug', $slug)

    ->first();

    /*
    |--------------------------------------------------------------------------
    | PLAN NOT FOUND
    |--------------------------------------------------------------------------
    */

    if (!$plan) {

        return response()->json([

            'status' => false,

            'message' => 'Insurance plan not found',
        ], 404);
    }

    /*
    |--------------------------------------------------------------------------
    | SUCCESS RESPONSE
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'status' => true,

        'plan' => $plan,
    ]);
}

public function store(Request $request)
{
    $request->validate([
        'category' => 'required|in:health,car,life,travel,bike,home,business,investment',
        'plan_name' => 'required|string|max:255',
        'slug' => 'required|string|unique:insurance_plans,slug',
        'company_name' => 'required|string|max:255',
        'starting_price' => 'required|numeric',

        // IMAGE VALIDATION
        'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    /*
    |--------------------------------------------------------------------------
    | UPLOAD LOGO
    |--------------------------------------------------------------------------
    */

    $logoPath = null;

    if ($request->hasFile('logo')) {

        // STORE IMAGE IN storage/app/public/plans
        $logoPath = $request->file('logo')
            ->store('plans', 'public');
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE PLAN
    |--------------------------------------------------------------------------
    */

    $plan = InsurancePlan::create([

        'category' => $request->category,

        'plan_name' => $request->plan_name,

        'slug' => $request->slug,

        'company_name' => $request->company_name,

        // SAVE FILE PATH
        'logo' => $logoPath,

        'short_description' => $request->short_description,

        'description' => $request->description,

        'starting_price' => $request->starting_price,

        'cashless_hospitals' => $request->cashless_hospitals,

        'claim_ratio' => $request->claim_ratio,

        'is_featured' => $request->is_featured ?? false,

        'is_popular' => $request->is_popular ?? false,

        'status' => true,
    ]);

    return response()->json([
        'status' => true,

        'message' => 'Insurance plan created successfully',

        'plan' => $plan,
    ]);
}


public function storeCoverage(Request $request)
{
    $request->validate([
        'insurance_plan_id' => 'required|exists:insurance_plans,id',
        'coverage_name' => 'required|string|max:255',
        'coverage_amount' => 'required|numeric',
        'one_year_price' => 'required|numeric',
    ]);

    $coverage = InsuranceCoverage::create([
        'insurance_plan_id' => $request->insurance_plan_id,
        'coverage_name' => $request->coverage_name,
        'coverage_amount' => $request->coverage_amount,
        'one_year_price' => $request->one_year_price,
        'two_year_price' => $request->two_year_price,
        'three_year_price' => $request->three_year_price,
        'is_recommended' => $request->is_recommended ?? false,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Coverage added successfully',
        'coverage' => $coverage,
    ]);
} 


public function storeFeature(Request $request)
{
    $request->validate([
        'insurance_plan_id' => 'required|exists:insurance_plans,id',
        'feature' => 'required|string|max:255',
    ]);

    $feature = InsuranceFeature::create([
        'insurance_plan_id' => $request->insurance_plan_id,
        'feature' => $request->feature,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Feature added successfully',
        'feature' => $feature,
    ]);
} 


public function storeRider(Request $request)
{
    $request->validate([
        'insurance_plan_id' => 'required|exists:insurance_plans,id',
        'title' => 'required|string|max:255',
        'price' => 'required|numeric',
    ]);

    $rider = InsuranceRider::create([
        'insurance_plan_id' => $request->insurance_plan_id,
        'title' => $request->title,
        'description' => $request->description,
        'price' => $request->price,
        'status' => true,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Rider added successfully',
        'rider' => $rider,
    ]);
} 



}