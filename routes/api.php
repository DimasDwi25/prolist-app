<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Marketing\MarketingClientController;
use App\Http\Controllers\API\Marketing\MarketingDashboardController;
use App\Http\Controllers\API\Marketing\MarketingQuotationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (SPA Sanctum)
|--------------------------------------------------------------------------
| Semua request dari React harus include withCredentials:true
| supaya cookie session dan CSRF token ikut terkirim.
|--------------------------------------------------------------------------
*/

// Login & CSRF cookie
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user', [AuthController::class, 'me']);

// Semua route berikut butuh auth:sanctum
Route::middleware('auth:api')->group(function () {
    Route::middleware('role:super_admin,marketing_director,engineering_director,supervisor marketing,manager_marketing,sales_supervisor,marketing_admin,marketing_estimator')
        ->group(function () {
            Route::get('/marketing', [MarketingDashboardController::class, 'index']);

            // Client CRUD
            Route::get('/clients', [MarketingClientController::class, 'index']);
            Route::post('/clients', [MarketingClientController::class, 'store']);
            Route::put('/clients/{client}', [MarketingClientController::class, 'update']);
            Route::delete('/clients/{client}', [MarketingClientController::class, 'destroy']);

            //quotation CRUD
            Route::get('/quotations', [MarketingQuotationController::class, 'index']);
            Route::get('/quotations/{quotation}', [MarketingQuotationController::class, 'show']);
            Route::post('/quotations', [MarketingQuotationController::class, 'store']);
            Route::put('/quotations/{quotation}', [MarketingQuotationController::class, 'update']);
            Route::delete('/quotations/{quotation}', [MarketingQuotationController::class, 'destroy']);

            Route::get('/ajax-clients', [MarketingQuotationController::class, 'ajaxClients']);
        });
});
