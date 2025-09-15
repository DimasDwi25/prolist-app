<?php

use App\Http\Controllers\API\ApprovallController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Marketing\MarketingCategorieProject;
use App\Http\Controllers\API\Marketing\MarketingClientController;
use App\Http\Controllers\API\Marketing\MarketingDashboardController;
use App\Http\Controllers\API\Marketing\MarketingPhcApiController;
use App\Http\Controllers\API\Marketing\MarketingProjectController;
use App\Http\Controllers\API\Marketing\MarketingQuotationController;
use App\Http\Controllers\API\Marketing\MarketingReportApiController;
use App\Http\Controllers\API\Marketing\MarketingStatusProjectController;
use App\Http\Controllers\API\Marketing\SalesReportApiController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\users\UsersController;
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

    Route::get('/users', [UsersController::class, 'index']);
    Route::get('/phc/users/engineering', [UsersController::class, 'engineeringUsers']);
    Route::get('/phc/users/marketing', [UsersController::class, 'marketingUsers']);
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
            Route::get('/quotations/next-number', [MarketingQuotationController::class, 'nextNumber']);
            Route::get('/quotations/{quotation}', [MarketingQuotationController::class, 'show']);
            Route::post('/quotations', [MarketingQuotationController::class, 'store']);
            Route::put('/quotations/{quotation}', [MarketingQuotationController::class, 'update']);
            Route::delete('/quotations/{quotation}', [MarketingQuotationController::class, 'destroy']);

            Route::get('/categories-project', [MarketingCategorieProject::class, 'index']);   // GET all
            Route::post('/categories-project', [MarketingCategorieProject::class, 'store']); // POST create
            Route::get('/categories-project/{category}', [MarketingCategorieProject::class, 'show']); // GET detail
            Route::put('/categories-project/{category}', [MarketingCategorieProject::class, 'update']); // PUT update
            Route::delete('/categories-project/{category}', [MarketingCategorieProject::class, 'destroy']); // DELETE

            Route::get('/status-projects', [MarketingStatusProjectController::class, 'index']);
            Route::post('/status-projects', [MarketingStatusProjectController::class, 'store']);
            Route::get('/status-projects/{statusProject}', [MarketingStatusProjectController::class, 'show']);
            Route::put('/status-projects/{statusProject}', [MarketingStatusProjectController::class, 'update']);
            Route::delete('/status-projects/{statusProject}', [MarketingStatusProjectController::class, 'destroy']);

            Route::get('/sales-report', [SalesReportApiController::class, 'index']);
            Route::get('/marketing-report', [MarketingReportApiController::class, 'index']);

            Route::get('/projects/generate-number', [MarketingProjectController::class, 'generateNumber']);
            Route::get('/projects', [MarketingProjectController::class, 'index']);
            Route::post('/projects', [MarketingProjectController::class, 'store']);
            Route::get('/projects/{project}', [MarketingProjectController::class, 'show']);
            Route::put('/projects/{project}', [MarketingProjectController::class, 'update']);
            Route::delete('/projects/{project}', [MarketingProjectController::class, 'destroy']);

            Route::post('/phc', [MarketingPhcApiController::class, 'store']);

            Route::get('/notifications', [NotificationController::class, 'index']);
            Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

            // List semua approval user
            Route::get('approvals', [ApprovallController::class, 'index']);
            // Detail approval
            Route::get('approvals/{id}', [ApprovallController::class, 'show']);
            // Update status dengan pin
            Route::post('approvals/{id}/status', [ApprovallController::class, 'updateStatus']);

            
           

            Route::get('/ajax-clients', [MarketingQuotationController::class, 'ajaxClients']);
        });
});
