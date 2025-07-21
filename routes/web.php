<?php

use App\Exports\ClientsExport;
use App\Exports\ProjectsExport;
use App\Exports\QuotationsExport;
use App\Exports\StatusProjectExport;
use App\Http\Controllers\Auth\LoginRedirectController;
use App\Http\Controllers\Engineer\EngineerDashboardController;
use App\Http\Controllers\Engineer\EngineerProjectController;
use App\Http\Controllers\Engineer\WorkOrderController;
use App\Http\Controllers\Marketing\CategorieProjectController;
use App\Http\Controllers\Marketing\ClientController;
use App\Http\Controllers\Marketing\PhcController;
use App\Http\Controllers\Marketing\ProjectController;
use App\Http\Controllers\Marketing\QuotationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController\ProjectControllerDashboardController;
use App\Http\Controllers\ProjectController\ProjectControllerPhcController;
use App\Http\Controllers\ProjectController\ProjectControllerProjectController;
use App\Http\Controllers\ProjectController\ProjectControllerWorkOrderController;
use App\Http\Controllers\ProjectLogController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\DepartmentController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\Marketing\DashboardController as MarketingDashboardController;
use App\Http\Controllers\supervisor_marketing\MarketingReportController;
use App\Http\Controllers\supervisor_marketing\SalesReportController;
use App\Http\Controllers\supervisor_marketing\SupervisorCategorieProjectController;
use App\Http\Controllers\supervisor_marketing\SupervisorClientController;
use App\Http\Controllers\supervisor_marketing\SupervisorDashboardController;
use App\Http\Controllers\supervisor_marketing\SupervisorPhcController;
use App\Http\Controllers\supervisor_marketing\SupervisorProjectController;
use App\Http\Controllers\supervisor_marketing\SupervisorQuotationController;
use App\Imports\ProjectsImport;
use App\Imports\QuotationsImport;
use App\Models\Project;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth']]);

Route::get('/', function () {
    return redirect()->route('login');
});
Route::post('/login', [LoginRedirectController::class, 'store']);

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');

    //department
    Route::get('/admin/department', [DepartmentController::class, 'index'])->name('admin.department');

    Route::get('/admin/department/create', [DepartmentController::class, 'create'])->name('department.create');
    Route::post('/admin/department/store', [DepartmentController::class, 'store'])->name('department.store');

    Route::get('/admin/department/edit/{department}', [DepartmentController::class, 'edit'])->name('department.edit');
    Route::put('/admin/department/update/{department}', [DepartmentController::class, 'update'])->name('department.update');
    Route::delete('/admin/department/delete/{department}', [DepartmentController::class, 'destroy'])->name('department.destroy');

    Route::get('/admin/role', [RoleController::class, 'index'])->name('admin.role');
    Route::get('/admin/role/create', [RoleController::class, 'create'])->name('role.create');
    Route::post('admin/role/store', [RoleController::class, 'store'])->name('role.store');

    Route::get('/admin/role/edit/{role}', [RoleController::class, 'edit'])->name('role.edit');
    Route::put('/admin/role/update/{role}', [RoleController::class, 'update'])->name('role.update');

    Route::delete('/admin/role/delete/{role}', [RoleController::class, 'destroy'])->name('role.destroy');

    Route::get('/admin/user', [UserController::class, 'index'])->name('admin.user');
    Route::get('/admin/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/admin/user/store', [UserController::class, 'store'])->name('user.store');

    Route::get('/admin/user/edit/{user}', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/admin/user/update/{user}', [UserController::class, 'update'])->name('user.update');

    Route::delete('/admin/user/delete/{user}', [UserController::class, 'destroy'])->name('user.destroy');

});

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::delete('/client/delete/{client}', [SupervisorClientController::class, 'destroy'])->name('client.destroy');
});

Route::middleware(['auth', 'role:supervisor marketing,super_admin'])->group(function () {
    Route::get('/supervisor-marketing', [SupervisorDashboardController::class, 'index'])->name('supervisor.dashboard');

    Route::get('/client', [SupervisorClientController::class, 'index'])->name('supervisor.client');
    Route::get('/client/create', [SupervisorClientController::class, 'create'])->name('client.create');
    Route::post('/client/store', [SupervisorClientController::class, 'store'])->name('client.store');
    Route::get('/client/edit/{client}', [SupervisorClientController::class, 'edit'])->name('client.edit');
    Route::put('/client/update/{client}', [SupervisorClientController::class, 'update'])->name('client.update');
    Route::get('/client/data', [SupervisorClientController::class, 'getData'])->name('client.data');
    Route::post('/client/import', [SupervisorClientController::class, 'import'])->name('client.import');

    Route::get('/categorie-project', [SupervisorCategorieProjectController::class, 'index'])->name('supervisor.category');
    Route::get('/categorie-project/create', [SupervisorCategorieProjectController::class, 'create'])->name('category.create');
    Route::post('/categorie-project/store', [SupervisorCategorieProjectController::class, 'store'])->name('category.store');
    Route::get('/categorie-project/edit/{category}', [SupervisorCategorieProjectController::class, 'edit'])->name('category.edit');
    Route::put('/categorie-project/update/{category}', [SupervisorCategorieProjectController::class, 'update'])->name('category.update');
    Route::delete('/categorie-project/delete/{category', [SupervisorCategorieProjectController::class, 'destroy'])->name('category.destroy');

    Route::get('/quotation', [SupervisorQuotationController::class, 'index'])->name('quotation.index');
    Route::get('/quotation/create', [SupervisorQuotationController::class, 'create'])->name('quotation.create');
    Route::post('/quotation/store', [SupervisorQuotationController::class, 'store'])->name('quotation.store');
    Route::get('/quotation/edit/{quotation}', [SupervisorQuotationController::class, 'edit'])->name('quotation.edit');
    Route::put('/quotation/update/{quotation}', [SupervisorQuotationController::class, 'update'])->name('quotation.update');
    Route::delete('/quotation/destroy/{quotation}', [SupervisorQuotationController::class, 'destroy'])->name('quotation.destroy');
    Route::get('/quotation/show/{quotation}', [SupervisorQuotationController::class, 'show'])->name('quotation.show');
    Route::patch('/quotation/{quotation}/status', [SupervisorQuotationController::class, 'updateStatus'])->name('quotation.updateStatus');
    Route::get('/ajax/clients', [SupervisorQuotationController::class, 'ajaxClients'])
    ->name('ajax.clients');

    Route::get('/marketing-report', [MarketingReportController::class, 'index'])->name('supervisor.marketing.report');
    Route::get('/sales-report', [SalesReportController::class, 'index'])->name('supervisor.sales.report');

    Route::get('/project', [SupervisorProjectController::class, 'index'])->name('supervisor.project');
    Route::get('/project/create', [SupervisorProjectController::class, 'create'])->name('project.create');
    Route::post('/project/store', [SupervisorProjectController::class, 'store'])->name('project.store');
    Route::get('/project/edit/{project}', [SupervisorProjectController::class, 'edit'])->name('project.edit');
    Route::post('/project/update/{project}', [SupervisorProjectController::class, 'update'])->name('project.update');
    Route::delete('/project/destroy/{project}', [SupervisorProjectController::class, 'destroy'])->name('project.destroy');
    Route::get('/project/view/{project}', [SupervisorProjectController::class, 'show'])->name('supervisor.project.show');



    Route::get('/project/phc/{project}', [SupervisorPhcController::class, 'create'])->name('phc');
    Route::post('/project/phc/store', [SupervisorPhcController::class, 'store'])->name('phc.store');
    Route::get('/phc/edit/{phc}', [SupervisorPhcController::class, 'edit'])->name('phc.edit');
    Route::put('/phc/update/{phc}', [SupervisorPhcController::class, 'update'])->name('phc.update');

    Route::get('/client/export', function () {
        return Excel::download(new ClientsExport, 'clients.xlsx');
    })->name('client.export');

    Route::get('/quotation/export', function () {
        return Excel::download(new QuotationsExport, 'quotations.xlsx');
    })->name('quotation.export');

    Route::post('/quotation/import', function (Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        Excel::import(new QuotationsImport, $request->file('file'));

        return back()->with('success', 'Quotation imported successfully.');
    })->name('quotation.import');

    Route::get('/projects/export', function () {
        return Excel::download(new ProjectsExport, 'projects.xlsx');
    });

    Route::post('/projects/import', function () {
        Excel::import(new ProjectsImport, request()->file('file'));
        return back()->with('success', 'Import berhasil!');
    });

    Route::get('/status-projects/export', function () {
        return Excel::download(new StatusProjectExport, 'status_projects.xlsx');
    });

});

Route::middleware(['auth', 'role:super_admin,supervisor marketing,engineer'])->group(function () {
    Route::get('/phc/show/{phc}', [SupervisorPhcController::class, 'show'])->name('phc.show');
    Route::get('/projects/{id}/logs', [ProjectLogController::class, 'show'])->name('projects.logs');
});

Route::middleware(['auth', 'role:engineer'])->group(function () {
    Route::get('/engineer', [EngineerDashboardController::class, 'index'])->name('engineer.dashboard');

    Route::get('/engineer/project', [EngineerProjectController::class, 'index'])->name('engineer.project');
    Route::get('/engineer/project/view/{project}', [EngineerProjectController::class, 'show'])->name('engineer.project.show');

    Route::get('/engineer/work-order', [WorkOrderController::class, 'index'])->name('engineer.work_order');
    Route::get('/engineer/work-order/create', [WorkOrderController::class, 'create'])->name('work-orders.create');
    Route::post('/engineer/work-order/store', [WorkOrderController::class, 'store'])->name('work-orders.store');
    Route::get('/engineer/work-order/edit/{workOrder}', [WorkOrderController::class, 'edit'])->name('work-orders.edit');
    Route::put('/engineer/work-order/update/{workOrder}', [WorkOrderController::class, 'update'])->name('work-orders.update');
    Route::delete('/engineer/work-order/delete/{workOrder}', [WorkOrderController::class, 'destroy'])->name('work_orders.destroy');

    // Route::post('/work-orders/{id}/add-log', [WorkOrderController::class, 'insertLogFromWO'])->name('work-orders.insert-log');

    Route::get('/projects/search', [WorkOrderController::class, 'search'])->name('projects.search');
    Route::get('/projects/{project}/client', [WorkOrderController::class, 'getClient']);

});

Route::middleware(['auth', 'role:project controller'])->group(function () {
    Route::get('/project-controller', [ProjectControllerDashboardController::class, 'index'])->name('project_controller.dashboard');

    Route::get('/project-controller/project', [ProjectControllerProjectController::class, 'index'])->name('project_controller.project.index');
    Route::get('/project-controller/project/view/{project}', [ProjectControllerProjectController::class, 'show'])->name('project_controller.project.show');

    Route::get('/project-controller/phc/show/{phc}', [ProjectControllerPhcController::class, 'show'])->name('project_controller.phc.show');

    Route::get('/project-controller/work-order', [ProjectControllerWorkOrderController::class, 'index'])->name('project_controller.work_order');
    Route::get('/project-controller/work-order/create', [ProjectControllerWorkOrderController::class, 'create'])->name('project_controller.work-orders.create');
    Route::post('/project-controller/work-order/store', [ProjectControllerWorkOrderController::class, 'store'])->name('project_controller.work-orders.store');
    Route::get('/project-controller/work-order/edit/{workOrder}', [ProjectControllerWorkOrderController::class, 'edit'])->name('project_controller.work-orders.edit');
    Route::put('/project-controller/work-order/update/{workOrder}', [ProjectControllerWorkOrderController::class, 'update'])->name('project_controller.work-orders.update');
    Route::delete('/project-controller/work-order/delete/{workOrder}', [ProjectControllerWorkOrderController::class, 'destroy'])->name('project_controller.work_orders.destroy');

    // Endpoint AJAX ambil nama client
    Route::get('/projects/{project}/client', [ProjectControllerWorkOrderController::class, 'getClient'])->name('project_controller.projects.client');
});


require __DIR__ . '/auth.php';
