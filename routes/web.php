<?php

use App\Http\Controllers\Auth\LoginRedirectController;
use App\Http\Controllers\Engineer\EngineerDashboardController;
use App\Http\Controllers\Engineer\EngineerProjectController;
use App\Http\Controllers\Marketing\CategorieProjectController;
use App\Http\Controllers\Marketing\ClientController;
use App\Http\Controllers\Marketing\PhcController;
use App\Http\Controllers\Marketing\ProjectController;
use App\Http\Controllers\Marketing\QuotationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectLogController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\DepartmentController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\Marketing\DashboardController as MarketingDashboardController;
use Illuminate\Support\Facades\Route;

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
    Route::delete('/client/delete/{client}', [ClientController::class, 'destroy'])->name('client.destroy');
});

Route::middleware(['auth', 'role:marketing,super_admin'])->group(function () {
    Route::get('/marketing', [MarketingDashboardController::class, 'index'])->name('marketing.dashboard');

    Route::get('/client', [ClientController::class, 'index'])->name('marketing.client');
    Route::get('/client/create', [ClientController::class, 'create'])->name('client.create');
    Route::post('/client/store', [ClientController::class, 'store'])->name('client.store');
    Route::get('/client/edit/{client}', [ClientController::class, 'edit'])->name('client.edit');
    Route::put('/client/update/{client}', [ClientController::class, 'update'])->name('client.update');

    Route::get('/categorie-project', [CategorieProjectController::class, 'index'])->name('marketing.category');
    Route::get('/categorie-project/create', [CategorieProjectController::class, 'create'])->name('category.create');
    Route::post('/categorie-project/store', [CategorieProjectController::class, 'store'])->name('category.store');
    Route::get('/categorie-project/edit/{category}', [CategorieProjectController::class, 'edit'])->name('category.edit');
    Route::put('/categorie-project/update/{category}', [CategorieProjectController::class, 'update'])->name('category.update');
    Route::delete('/categorie-project/delete/{category', [CategorieProjectController::class, 'destroy'])->name('category.destroy');

    Route::get('/quotation', [QuotationController::class, 'index'])->name('quotation.index');
    Route::get('/quotation/create', [QuotationController::class, 'create'])->name('quotation.create');
    Route::post('/quotation/store', [QuotationController::class, 'store'])->name('quotation.store');
    Route::get('/quotation/edit/{quotation}', [QuotationController::class, 'edit'])->name('quotation.edit');
    Route::put('/quotation/update/{quotation}', [QuotationController::class, 'update'])->name('quotation.update');
    Route::delete('/quotation/destroy/{quotation}', [QuotationController::class, 'destroy'])->name('quotation.destroy');
    Route::get('/quotation/show/{quotation}', [QuotationController::class, 'show'])->name('quotation.show');
    Route::patch('/quotation/{quotation}/status', [QuotationController::class, 'updateStatus'])->name('quotation.updateStatus');

    Route::get('/project', [ProjectController::class, 'index'])->name('marketing.project');
    Route::get('/project/create', [ProjectController::class, 'create'])->name('project.create');
    Route::post('/project/store', [ProjectController::class, 'store'])->name('project.store');
    Route::get('/project/edit/{project}', [ProjectController::class, 'edit'])->name('project.edit');
    Route::post('/project/update/{project}', [ProjectController::class, 'update'])->name('project.update');
    Route::delete('/project/destroy/{project}', [ProjectController::class, 'destroy'])->name('project.destroy');

    

    Route::get('/project/phc/{project}', [PhcController::class, 'create'])->name('phc');
    Route::post('/project/phc/store', [PhcController::class, 'store'])->name('phc.store');
    Route::get('/phc/edit/{phc}', [PhcController::class, 'edit'])->name('phc.edit');
    Route::put('/phc/update/{phc}', [PhcController::class, 'update'])->name('phc.update');

    

    
});

Route::middleware(['auth', 'role:super_admin,marketing,engineer'])->group(function () {
    Route::get('/phc/show/{phc}', [PhcController::class, 'show'])->name('phc.show');
    Route::get('/project/view/{project}', [ProjectController::class, 'show'])->name('project.show');
    Route::get('/projects/{id}/logs', [ProjectLogController::class, 'show'])->name('projects.logs');
});

Route::middleware(['auth', 'role:engineer'])->group(function () {
    Route::get('/engineer', [EngineerDashboardController::class, 'index'])->name('engineer.dashboard');

    Route::get('/engineer/project', [EngineerProjectController::class, 'index'])->name('engineer.project');
    Route::get('/engineer/project/view/{project}', [EngineerProjectController::class, 'show'])->name('engineer.project.show');
});


require __DIR__ . '/auth.php';
