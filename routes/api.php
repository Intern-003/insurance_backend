<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InsurancePlanController;

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
| PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});