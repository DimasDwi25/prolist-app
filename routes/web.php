<?php

use App\Exports\ClientsExport;
use App\Exports\ProjectsExport;
use App\Exports\QuotationsExport;
use App\Exports\StatusProjectExport;
use App\Http\Controllers\Auth\LoginRedirectController;
use App\Http\Controllers\Engineer\EngineerDashboardController;
use App\Http\Controllers\Engineer\EngineerProjectController;
use App\Http\Controllers\Engineer\WorkOrderController;
use App\Http\Controllers\MarketingDirector\DashboardController as MarketingDirectorDashboardController;
use App\Http\Controllers\MarketingDirector\MarketingDirectorClientController;
use App\Http\Controllers\MarketingDirector\MarketingDirectorMarketingReportController;
use App\Http\Controllers\MarketingDirector\MarketingDirectorPhcController;
use App\Http\Controllers\MarketingDirector\MarketingDirectorProjectController;
use App\Http\Controllers\MarketingDirector\MarketingDirectorQuotationController;
use App\Http\Controllers\MarketingDirector\MarketingDirectorSalesReportController;
use App\Http\Controllers\ProjectController\ProjectControllerDashboardController;
use App\Http\Controllers\ProjectController\ProjectControllerPhcController;
use App\Http\Controllers\ProjectController\ProjectControllerProjectController;
use App\Http\Controllers\ProjectController\ProjectControllerWorkOrderController;
use App\Http\Controllers\ProjectController\ProjectScheduleController;
use App\Http\Controllers\ProjectController\ProjectScheduleTaskController;
use App\Http\Controllers\ProjectLogController;
use App\Http\Controllers\ProjectManager\ProjectManagerDashboardController;
use App\Http\Controllers\ProjectManager\ProjectManagerPhcController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\DepartmentController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\supervisor_marketing\ImportQuotationController;
use App\Http\Controllers\supervisor_marketing\MarketingReportController;
use App\Http\Controllers\supervisor_marketing\SalesReportController;
use App\Http\Controllers\supervisor_marketing\SupervisorCategorieProjectController;
use App\Http\Controllers\supervisor_marketing\SupervisorClientController;
use App\Http\Controllers\supervisor_marketing\SupervisorDashboardController;
use App\Http\Controllers\supervisor_marketing\SupervisorPhcController;
use App\Http\Controllers\supervisor_marketing\SupervisorProjectController;
use App\Http\Controllers\supervisor_marketing\SupervisorQuotationController;
use App\Http\Livewire\PhcValidation;
use App\Imports\ProjectsImport;
use App\Livewire\NotificationsList;
use App\Livewire\ProjectController\MasterScopeOfWork;
use App\Livewire\ProjectController\MasterTasks;
use App\Livewire\ProjectController\ProjectSchedules;
use App\Livewire\ProjectController\WeeklyProgressAll;
use App\Livewire\ProjectController\WeeklyProgressBoard;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\UserExportController;
use App\Livewire\MarketingDirector\MarketingReportTable;
use App\Livewire\MarketingDirector\SalesReportTable;
use App\Livewire\ProjectController\ManPowerAllocationForm;
use App\Livewire\SupervisorMarketing\MasterStatusProject;
use Illuminate\Http\Request;
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
    Route::get('/admin/department/delete/{department}', [DepartmentController::class, 'destroy'])->name('department.destroy');
    Route::post('/admin/department/import', [DepartmentController::class, 'import'])->name('department.import');

    Route::get('/admin/role', [RoleController::class, 'index'])->name('admin.role');
    Route::get('/admin/role/create', [RoleController::class, 'create'])->name('role.create');
    Route::post('admin/role/store', [RoleController::class, 'store'])->name('role.store');

    Route::get('/admin/role/edit/{role}', [RoleController::class, 'edit'])->name('role.edit');
    Route::put('/admin/role/update/{role}', [RoleController::class, 'update'])->name('role.update');

    Route::get('/admin/role/delete/{role}', [RoleController::class, 'destroy'])->name('role.destroy');

    Route::get('/admin/user', [UserController::class, 'index'])->name('admin.user');
    Route::get('/admin/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/admin/user/store', [UserController::class, 'store'])->name('user.store');

    Route::get('/admin/user/edit/{user}', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/admin/user/update/{user}', [UserController::class, 'update'])->name('user.update');

    Route::get('/admin/user/delete/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/users/import', [UserController::class, 'import'])->name('users.import');



    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');

});

Route::middleware(['auth', 'role:super_admin,marketing_director,engineering_director,supervisor marketing,manager_marketing,sales_supervisor,marketing_admin'])->group(function () {
    Route::get('/marketing', [SupervisorDashboardController::class, 'index'])->name('marketing.dashboard');
    Route::get('/marketing-director', [MarketingDirectorDashboardController::class, 'index'])->name('marketing_director.dashboard');

    Route::get('/client', [SupervisorClientController::class, 'index'])->name('supervisor.client');
    Route::get('/client/create', [SupervisorClientController::class, 'create'])->name('client.create');
    Route::post('/client/store', [SupervisorClientController::class, 'store'])->name('client.store');
    Route::get('/client/edit/{client}', [SupervisorClientController::class, 'edit'])->name('client.edit');
    Route::put('/client/update/{client}', [SupervisorClientController::class, 'update'])->name('client.update');
    Route::get('/client/data', [SupervisorClientController::class, 'getData'])->name('client.data');
    Route::post('/client/import', [SupervisorClientController::class, 'import'])->name('client.import');
    Route::get('/clients/{client}', [SupervisorClientController::class, 'show'])->name('client.show');
    Route::get('/client/delete/{client}', [SupervisorClientController::class, 'destroy'])->name('client.destroy');


    Route::get('/categorie-project', [SupervisorCategorieProjectController::class, 'index'])->name('supervisor.category');
    Route::get('/categorie-project/create', [SupervisorCategorieProjectController::class, 'create'])->name('category.create');
    Route::post('/categorie-project/store', [SupervisorCategorieProjectController::class, 'store'])->name('category.store');
    Route::get('/categorie-project/edit/{category}', [SupervisorCategorieProjectController::class, 'edit'])->name('category.edit');
    Route::put('/categorie-project/update/{category}', [SupervisorCategorieProjectController::class, 'update'])->name('category.update');
    Route::get('/categorie-project/delete/{category', [SupervisorCategorieProjectController::class, 'destroy'])->name('category.destroy');

    Route::get('/quotation', [SupervisorQuotationController::class, 'index'])->name('quotation.index');
    Route::get('/quotation/create', [SupervisorQuotationController::class, 'create'])->name('quotation.create');
    Route::post('/quotation/store', [SupervisorQuotationController::class, 'store'])->name('quotation.store');
    Route::get('/quotation/edit/{quotation}', [SupervisorQuotationController::class, 'edit'])->name('quotation.edit');
    Route::put('/quotation/update/{quotation}', [SupervisorQuotationController::class, 'update'])->name('quotation.update');
    Route::get('/quotation/destroy/{quotation}', [SupervisorQuotationController::class, 'destroy'])->name('quotation.destroy');
    Route::get('/quotation/show/{quotation}', [SupervisorQuotationController::class, 'show'])->name('quotation.show');
    Route::patch('/quotation/{quotation}/status', [SupervisorQuotationController::class, 'updateStatus'])->name('quotation.updateStatus');
    Route::get('/ajax/clients', [SupervisorQuotationController::class, 'ajaxClients'])
        ->name('ajax.clients');

    Route::post('/quotation/import', [ImportQuotationController::class, 'import'])->name('quotation.import');

    Route::get('/marketing-report', [MarketingReportController::class, 'index'])->name('supervisor.marketing.report');
    Route::get('/sales-report', [SalesReportController::class, 'index'])->name('supervisor.sales.report');

    Route::get('/project', [SupervisorProjectController::class, 'index'])->name('supervisor.project');
    Route::get('/project/create', [SupervisorProjectController::class, 'create'])->name('project.create');
    Route::post('/project/store', [SupervisorProjectController::class, 'store'])->name('project.store');
    Route::get('/project/edit/{project}', [SupervisorProjectController::class, 'edit'])->name('project.edit');
    Route::put('/project/update/{project}', [SupervisorProjectController::class, 'update'])->name('project.update');
    Route::delete('/project/destroy/{project}', [SupervisorProjectController::class, 'destroy'])->name('project.destroy');
    Route::get('/project/view/{project}', [SupervisorProjectController::class, 'show'])->name('supervisor.project.show');

    Route::get('/master-status-project', MasterStatusProject::class)->name('status_project');

    // Ambil nomor project terbaru (PN atau CO)
    Route::get('/projects/generate-number', function (Request $request) {
        $isCO = filter_var($request->query('is_co'), FILTER_VALIDATE_BOOLEAN);

        $lastProject = \App\Models\Project::latest()->first();
        $nextNumber = $lastProject ? $lastProject->pn_number + 1 : 25001; // contoh start: 25xxx

        return [
            'project_number' => \App\Models\Project::generateProjectNumber($nextNumber, $isCO),
            'pn_number' => $nextNumber
        ];
    });

    // Ambil info project induk
    Route::get('/projects/info/{pn_number}', function ($pn_number) {
        return \App\Models\Project::with(['category', 'quotation.client'])
            ->where('pn_number', $pn_number)
            ->firstOrFail();
    });




    Route::get('/project/phc/{project}', [SupervisorPhcController::class, 'create'])->name('phc');
    Route::post('/project/phc/store', [SupervisorPhcController::class, 'store'])->name('phc.store');
    Route::get('/phc/edit/{phc}', [SupervisorPhcController::class, 'edit'])->name('phc.edit');
    Route::put('/phc/update/{phc}', [SupervisorPhcController::class, 'update'])->name('phc.update');
    Route::get('/phc/show/{phc}', [SupervisorPhcController::class, 'show'])->name('phc.show');

    Route::get('/client/export', function () {
        return Excel::download(new ClientsExport, 'clients.xlsx');
    })->name('client.export');

    Route::get('/quotation/export', function () {
        return Excel::download(new QuotationsExport, 'quotations.xlsx');
    })->name('quotation.export');



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

    Route::get('/supervisor/projects/{id}/logs', [ProjectLogController::class, 'show'])->name('supervisor.projects.logs');

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
    Route::get('/engineer/work-order/delete/{workOrder}', [WorkOrderController::class, 'destroy'])->name('work_orders.destroy');

    // Route::post('/work-orders/{id}/add-log', [WorkOrderController::class, 'insertLogFromWO'])->name('work-orders.insert-log');

    Route::get('/projects/search', [WorkOrderController::class, 'search'])->name('projects.search');
    Route::get('/projects/{project}/client', [WorkOrderController::class, 'getClient']);

});

Route::middleware(['auth', 'role:engineering_director,project controller,engineer,engineering_manager'])->group(function () {
    Route::get('/engineer', [ProjectControllerDashboardController::class, 'index'])->name('engineer.dashboard');

    Route::get('/engineer/project', [ProjectControllerProjectController::class, 'index'])->name('engineer.project.index');
    Route::get('/engineer/project/view/{project}', [ProjectControllerProjectController::class, 'show'])->name('engineer.project.show');

    Route::get('/engineer/phc/show/{phc}', [ProjectControllerPhcController::class, 'show'])->name('engineer.phc.show');
    Route::get('/engineer/phc/edit/{phc}', [ProjectControllerPhcController::class, 'edit'])->name('engineer.phc.edit');
    Route::put('/engineer/phc/update/{phc}', [ProjectControllerPhcController::class, 'update'])->name('engineer.phc.update');

    Route::get('/engineer/work-order', [ProjectControllerWorkOrderController::class, 'index'])->name('engineer.work_order');
    Route::get('/engineer/work-order/create', [ProjectControllerWorkOrderController::class, 'create'])->name('engineer.work-orders.create');
    Route::post('/engineer/work-order/store', [ProjectControllerWorkOrderController::class, 'store'])->name('engineer.work-orders.store');
    Route::get('/engineer/work-order/edit/{workOrder}', [ProjectControllerWorkOrderController::class, 'edit'])->name('engineer.work-orders.edit');
    Route::put('/engineer/work-order/update/{workOrder}', [ProjectControllerWorkOrderController::class, 'update'])->name('engineer.work-orders.update');
    Route::get('/engineer/work-order/delete/{workOrder}', [ProjectControllerWorkOrderController::class, 'destroy'])->name('engineer.work_orders.destroy');

    Route::get('/engineer/{project}/man-power-allocation', ManPowerAllocationForm::class)->name('man-power');

    Route::get('/master-tasks', MasterTasks::class)->name('tasks');
    Route::get('/master-scope-of-work', MasterScopeOfWork::class)->name('scope_of_work');

    Route::get(
        '/projects/{project}/weekly-progress-all',
        \App\Livewire\ProjectController\WeeklyProgressAll::class
    )->name('projects.schedule.weekly-progress-all');


    // Endpoint AJAX ambil nama client
    Route::get('/projects/{project}/client', [ProjectControllerWorkOrderController::class, 'getClient'])->name('engineer.projects.client');
    Route::get('/projects/{id}/logs', [ProjectLogController::class, 'show'])->name('projects.logs');

    Route::prefix('projects/{project}')->name('projects.')->group(function () {
        Route::get('schedules', [ProjectScheduleController::class, 'index'])->name('schedules.index');
        Route::get('schedules/create', [ProjectScheduleController::class, 'create'])->name('schedules.create');
        Route::post('schedules', [ProjectScheduleController::class, 'store'])->name('schedules.store');
        Route::get('schedules/{schedule}/edit', [ProjectScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('schedules/{schedule}', [ProjectScheduleController::class, 'update'])->name('schedules.update');
        Route::get('schedules/{schedule}', [ProjectScheduleController::class, 'destroy'])->name('schedules.destroy');
    });

    Route::prefix('projects/{project}/schedules/{schedule}')
        ->name('projects.schedule-tasks.')
        ->group(function () {
            Route::get('tasks', [ProjectScheduleTaskController::class, 'index'])->name('index');
            Route::get('tasks/create', [ProjectScheduleTaskController::class, 'create'])->name('create');
            Route::post('tasks', [ProjectScheduleTaskController::class, 'store'])->name('store');
            Route::get('tasks/{task}/edit', [ProjectScheduleTaskController::class, 'edit'])->name('edit');
            Route::put('tasks/{task}', [ProjectScheduleTaskController::class, 'update'])->name('update');
            Route::get('tasks/{task}', [ProjectScheduleTaskController::class, 'destroy'])->name('destroy');
        });

    Route::get(
        'projects/{project}/schedules/{schedule}/tasks/{task}/weekly-progress',
        \App\Livewire\ProjectController\WeeklyProgress::class
    )
        ->name('projects.schedule-tasks.weekly-progress');
});

Route::middleware(['auth', 'role:project manager'])->group(function () {
    Route::get('/project-manager', [ProjectManagerDashboardController::class, 'index'])->name('project_manager.dashboard');
    Route::get('/project-manager/phc/show/{phc}', [ProjectManagerPhcController::class, 'show'])->name('project_manager.phc.show');
});


require __DIR__ . '/auth.php';
