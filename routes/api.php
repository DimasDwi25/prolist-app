<?php

use App\Http\Controllers\API\ApprovallController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Engineer\CategorieLogApiController;
use App\Http\Controllers\API\Engineer\DocumentApiController;
use App\Http\Controllers\API\Engineer\DocumentPreparationApiController;
use App\Http\Controllers\API\Engineer\EngineerDashboardApiController;
use App\Http\Controllers\API\Engineer\EngineerPhcDocumentiApi;
use App\Http\Controllers\API\Engineer\EngineerProjectApiController;
use App\Http\Controllers\API\Engineer\ManPowerAllocationApiController;
use App\Http\Controllers\API\Engineer\OutstandingProjectApiController;
use App\Http\Controllers\API\Engineer\PurposeWorkOrderApiController;
use App\Http\Controllers\API\Engineer\ScopeOfWorkProjectApiController;
use App\Http\Controllers\API\Engineer\WorkOrderApiController;
use App\Http\Controllers\API\LogController;
use App\Http\Controllers\API\Marketing\BillOfQuantityController;
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
use App\Http\Controllers\API\SUC\MaterialRequestApiController;
use App\Http\Controllers\API\SUC\PackingListApiController;
use App\Http\Controllers\API\users\DepartmentController;
use App\Http\Controllers\API\users\RoleController;
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

    Route::get('/engineer/dashboard', [EngineerDashboardApiController::class, 'index']);


    Route::get('/users', [UsersController::class, 'index']);
    Route::post('/users', [UsersController::class, 'store']);
    Route::get('/users/engineer-only', [UsersController::class, 'engineerOnly']);
    Route::get('/users/roleTypeTwoOnly', [UsersController::class, 'roleTypeTwoOnly']);
    Route::get('/users/manPowerUsers', [UsersController::class, 'manPowerUsers']);
    Route::get('/users/manPowerRoles', [UsersController::class, 'manPowerRoles']);
    Route::get('/users/{user}', [UsersController::class, 'show']);
    Route::put('/users/{user}', [UsersController::class, 'update']);
    Route::delete('/users/{user}', [UsersController::class, 'destroy']);

    // Roles
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/onlyType1',[RoleController::class, 'roleOnlyType1']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);

    // Departments
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::get('/departments/{id}', [DepartmentController::class, 'show']);
    Route::put('/departments/{id}', [DepartmentController::class, 'update']);
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);

    Route::get('/phc/users/engineering', [UsersController::class, 'engineeringUsers']);
    Route::get('/phc/users/marketing', [UsersController::class, 'marketingUsers']);
    

    
    
    // List semua approval user
    Route::get('approvals', [ApprovallController::class, 'index']);
    // Detail approval
    Route::get('approvals/{id}', [ApprovallController::class, 'show']);

    Route::post('approvals/log/{id}/status', [ApprovallController::class, 'updateStatusLog']);
    Route::post('approvals/wo/{id}/status', [ApprovallController::class, 'updateStatusWo']);
    Route::post('approvals/{id}/status', [ApprovallController::class, 'updateStatus']); 


    

    Route::get('/phc/{id}', [MarketingPhcApiController::class, 'show']);

    

    
    // Route::middleware('role:super_admin,marketing_director,engineering_director,supervisor marketing,manager_marketing,sales_supervisor,marketing_admin,marketing_estimator')
    //     ->group(function () {
            
    //     });

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
    
    Route::post('/projects', [MarketingProjectController::class, 'store']);
    
    Route::put('/projects/{project}', [MarketingProjectController::class, 'update']);
    Route::delete('/projects/{project}', [MarketingProjectController::class, 'destroy']);

    Route::post('/phc', [MarketingPhcApiController::class, 'store']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    

    Route::get('/projects/{projectId}/boq', [BillOfQuantityController::class, 'index']);
    Route::post('/projects/{projectId}/boq', [BillOfQuantityController::class, 'store']);
    Route::put('/boq/{id}', [BillOfQuantityController::class, 'update']);

    
    Route::put('/phc/{id}', [MarketingPhcApiController::class, 'update']);


    Route::get('/ajax-clients', [MarketingQuotationController::class, 'ajaxClients']);

    // Route::middleware('role:super_admin,marketing_director,engineering_director,supervisor marketing,manager_marketing,sales_supervisor,marketing_admin,marketing_estimator,project controller,project manager,warehouse')
    // ->group(function () {

        
    // });
    // Client CRUD
    Route::get('/clients', [MarketingClientController::class, 'index']);
    Route::get('/quotations', [MarketingQuotationController::class, 'index']);

    Route::get('/categories-project', [MarketingCategorieProject::class, 'index']);   // GET all

    Route::get('/status-projects', [MarketingStatusProjectController::class, 'index']);

    Route::get('/projects', [MarketingProjectController::class, 'index']);
    Route::get('/projects/{project}', [MarketingProjectController::class, 'show']);

    Route::get('/phcs/show/{phc}', [EngineerPhcDocumentiApi::class, 'show']);

    Route::get('/phcs/{phc}/document-preparations', [DocumentPreparationApiController::class, 'index']);

    Route::get('/document-phc', [DocumentApiController::class, 'index']);

    // Route::middleware('role:super_admin,marketing_director,engineering_director,project controller,project manager')
    // ->group(function () {
        
    // });

    Route::put('/phcs/{phc}', [EngineerPhcDocumentiApi::class, 'update']);
                
    Route::get('/document-phc/{id}', [DocumentApiController::class, 'show']); 
    Route::post('/document-phc', [DocumentApiController::class, 'store']);    
    Route::put('/document-phc/{id}', [DocumentApiController::class, 'update']); 
    Route::delete('/document-phc/{id}', [DocumentApiController::class, 'destroy']); 

    Route::put('/phcs/{phc}', [EngineerPhcDocumentiApi::class, 'update']);    
        
    Route::get('/document-phc', [DocumentApiController::class, 'index']);
    Route::get('/document-phc/{id}', [DocumentApiController::class, 'show']); 
    Route::post('/document-phc', [DocumentApiController::class, 'store']);    
    Route::put('/document-phc/{id}', [DocumentApiController::class, 'update']); 
    Route::delete('/document-phc/{id}', [DocumentApiController::class, 'destroy']);

    Route::get('/material-requests', [MaterialRequestApiController::class, 'index']);      
    Route::post('/material-requests', [MaterialRequestApiController::class, 'store']);    
    Route::get('/material-requests/{materialRequest}', [MaterialRequestApiController::class, 'show']); 
    Route::put('/material-requests/{materialRequest}', [MaterialRequestApiController::class, 'update']); 
    Route::patch('/material-requests/{materialRequest}', [MaterialRequestApiController::class, 'update']); 
    Route::delete('/material-requests/{materialRequest}', [MaterialRequestApiController::class, 'destroy']); 

    Route::post('/{materialRequest}/cancel', [MaterialRequestApiController::class, 'cancel']);
    Route::post('/{materialRequest}/complete', [MaterialRequestApiController::class, 'complete']);

    Route::get('/mr-summary', [MaterialRequestApiController::class, 'getMrSummary']);

    Route::get('/work-order/{pn_number}', [WorkOrderApiController::class, 'index']); // GET by PN
    Route::get('/work-orders/next-code', [WorkOrderApiController::class, 'nextCode']);
    Route::get('/work-order/detail/{id}', [WorkOrderApiController::class, 'show']);
    Route::get('/work-orders/{id}/pdf', [WorkOrderApiController::class, 'downloadPdf']);
    Route::post('/work-order', [WorkOrderApiController::class, 'store']);           // CREATE
    Route::put('/work-order/{id}', [WorkOrderApiController::class, 'update']);       // UPDATE
    Route::get('/wo-summary', [WorkOrderApiController::class, 'getWoSummary']);
    
    

    // List all allocations by project PN number
    Route::get('man-power/{pn_number}', [ManPowerAllocationApiController::class, 'index']);

    // Show single allocation
    Route::get('man-power/show/{id}', [ManPowerAllocationApiController::class, 'show']);

    // Create new allocation
    Route::post('man-power', [ManPowerAllocationApiController::class, 'store']);

    // Update allocation
    Route::put('man-power/{id}', [ManPowerAllocationApiController::class, 'update']);

    // Delete allocation
    Route::delete('man-power/{id}', [ManPowerAllocationApiController::class, 'destroy']);

    Route::get('/packing-lists/generate-number', [PackingListApiController::class, 'generateNumber']);
    Route::get('/packing-lists', [PackingListApiController::class, 'index']);

    Route::post('/packing-lists', [PackingListApiController::class, 'store']);
    Route::get('/packing-lists/{id}', [PackingListApiController::class, 'show']);
    Route::put('/packing-lists/{id}', [PackingListApiController::class, 'update']);
    Route::delete('/packing-lists/{id}', [PackingListApiController::class, 'destroy']);

    Route::get('/projects/{project}/logs', [LogController::class, 'index']);
    Route::get('/logs/{id}', [LogController::class, 'show']);
    Route::post('/logs', [LogController::class, 'store']);
    Route::put('/logs/{id}', [LogController::class, 'update']);
    Route::delete('/logs/{id}', [LogController::class, 'destroy']);
    Route::patch('/logs/{id}/close', [LogController::class, 'close']);

    // Tampilkan semua kategori
    Route::get('/categories-log', [CategorieLogApiController::class, 'index']);

    Route::get('/categories-log/{id}', [CategorieLogApiController::class, 'show']);

    Route::post('/categories-log', [CategorieLogApiController::class, 'store']);

    Route::put('/categories-log/{id}', [CategorieLogApiController::class, 'update']);

    Route::delete('/categories-log/{id}', [CategorieLogApiController::class, 'destroy']);

    

    Route::get('/scope-of-work', [ScopeOfWorkProjectApiController::class, 'index']);
    Route::post('/scope-of-work', [ScopeOfWorkProjectApiController::class, 'store']);
    Route::get('/scope-of-work/{id}', [ScopeOfWorkProjectApiController::class, 'show']);
    Route::put('/scope-of-work/{id}', [ScopeOfWorkProjectApiController::class, 'update']);
    Route::delete('/scope-of-work/{id}', [ScopeOfWorkProjectApiController::class, 'destroy']);

    Route::get('/purpose-work-orders', [PurposeWorkOrderApiController::class, 'index']);
    Route::get('/purpose-work-orders/{id}', [PurposeWorkOrderApiController::class, 'show']);
    Route::post('/purpose-work-orders', [PurposeWorkOrderApiController::class, 'store']);
    Route::put('/purpose-work-orders/{id}', [PurposeWorkOrderApiController::class, 'update']);
    Route::delete('/purpose-work-orders/{id}', [PurposeWorkOrderApiController::class, 'destroy']);

    Route::get('/project/man-power', [EngineerProjectApiController::class, 'engineerProjects']);

    Route::get('/outstanding-projects', [OutstandingProjectApiController::class, 'index']);

    
});     
