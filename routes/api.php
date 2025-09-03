<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Marketing\MarketingClientController;
use App\Http\Controllers\API\Marketing\MarketingDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::middleware(['auth:sanctum', 'role:super_admin,marketing_director,engineering_director,supervisor marketing,manager_marketing,sales_supervisor,marketing_admin,marketing_estimator'])
    ->group(function () {

        Route::get('/marketing', [MarketingDashboardController::class, 'index']);

        //client
        Route::get('/clients', [MarketingClientController::class, 'index']);
        Route::post('/clients', [MarketingClientController::class, 'store']);
        Route::put('/clients/{client}', [MarketingClientController::class, 'update']);
        Route::delete('/clients/{client}', [MarketingClientController::class, 'destroy']);
});