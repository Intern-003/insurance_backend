<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InsurancePlanController;
use App\Http\Controllers\Api\InsuranceProposalController;
use App\Http\Controllers\Api\InsuranceLeadController;
use App\Http\Controllers\Api\InsurancePlanSelectionController;
use App\Http\Controllers\Api\RenewalController;
/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/ha', function () {
    return 'insurance';
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| INSURANCE PUBLIC APIs
|--------------------------------------------------------------------------
*/

Route::get(
    '/insurance-plans/{category}',
    [InsurancePlanController::class, 'index']
);

Route::get(
    '/insurance-plan/{slug}',
    [InsurancePlanController::class, 'show']
);
Route::post(
    '/insurance-plan',
    [InsurancePlanController::class, 'store']
);

Route::post(
    '/insurance-coverage',
    [InsurancePlanController::class, 'storeCoverage']
);

Route::post(
    '/insurance-feature',
    [InsurancePlanController::class, 'storeFeature']
);

Route::post(
    '/insurance-rider',
    [InsurancePlanController::class, 'storeRider']
);

/*
|--------------------------------------------------------------------------
| INSURANCE PROPOSAL APIs
|--------------------------------------------------------------------------
*/

Route::post(
    '/store-insurance-proposal',
    [InsuranceProposalController::class, 'store']
);

Route::get(
    '/insurance-proposal/{application_number}',
    [InsuranceProposalController::class, 'show']
);

Route::post(
    '/complete-payment',
    [InsuranceProposalController::class, 'completePayment']
);




Route::get(
    '/invoice/{application_number}',
    [InsuranceProposalController::class, 'invoice']
); 

Route::post(
    '/store-insurance-lead',
    [InsuranceLeadController::class, 'store']
);
Route::post(
    '/store-plan-selection',
    [InsurancePlanSelectionController::class, 'store']
);



Route::prefix('renewals')->group(function () {

    Route::post('/verify', [RenewalController::class, 'verify']);

    Route::get('/policy/{id}', [RenewalController::class, 'getPolicy']);

    Route::post('/update-proposal/{id}', [RenewalController::class, 'updateProposal']);

});

Route::get(
    '/renewal-details/{proposal_id}',
    [RenewalController::class, 'renewalDetails']
);

Route::post(
    '/update-renewal',
    [RenewalController::class, 'updateRenewal']
);



/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});


 