<?php

use App\Http\Controllers\Admin\RelevantUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Boss\EmployeeController;
use App\Http\Controllers\BugalterController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentKpiController;
use App\Http\Controllers\DirectorProfileController;
use App\Http\Controllers\EmployeeDaysController;
use App\Http\Controllers\EmployeeKpiController;
use App\Http\Controllers\EmployeeProfileController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserKPIController;
use App\Http\Controllers\WorkingKpiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\MonthController;
use App\Models\RelevantUser;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/home', [DashboardController::class, 'index'])->name('home');

Route::post('/change-year', [SessionController::class, 'changeYear']);
Route::post('/change-month', [SessionController::class, 'changeMonth']);


Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::group(['prefix' => 'users'], function () {
        Route::get('/list', [EmployeesController::class, 'index'])->name('employees.list');
        Route::post('/add', [EmployeesController::class, 'add'])->name('employees.add');
        Route::post('/store', [EmployeesController::class, 'store'])->name('employees.store');
        Route::get('/edit/{id}', [EmployeesController::class, 'edit'])->name('employees.edit');
        Route::put('/update/{id}', [EmployeesController::class, 'update'])->name('employees.update');
        Route::post('/delete/{id}', [EmployeesController::class, 'delete'])->name('employees.delete');
        Route::delete('/destroy/{id}', [EmployeesController::class, 'destroy'])->name('employees.destroy');
        Route::get('/show/{id}', [EmployeesController::class, 'show'])->name('employees.show');
        Route::get('/create', [EmployeesController::class, 'create'])->name('employees.create');

    });

    Route::resource('roles', RoleController::class);
    Route::resource('works', WorkController::class);
    Route::resource('month', MonthController::class);

    Route::group(['prefix' => 'days'], function () {
        Route::post('/createday/{user}', [EmployeeDaysController::class, 'createday'])->name('days.createday');
        Route::get('/list', [EmployeeDaysController::class, 'list'])->name('days.list');

        Route::get('/behavior-list', [EmployeeDaysController::class, 'behavior'])->name('days.behavior');
        Route::post('/create-behavior/{user}', [EmployeeDaysController::class, 'createBehavior'])->name('days.create.behavior');

        Route::get('/activity-list', [EmployeeDaysController::class, 'activity'])->name('days.activity');
        Route::post('/create-activity/{user}', [EmployeeDaysController::class, 'createActivity'])->name('days.create.activity');
    });
    Route::group(['prefix' => 'director-profile'], function () {
        Route::get('/list', [DirectorProfileController::class, 'index'])->name('director.list');
        Route::get('/check-user/{type}/{employee}', [DirectorProfileController::class, 'check_user'])->name('director.check_user');
        Route::get('/employees', [DirectorProfileController::class, 'employees'])->name('director.employees');
        Route::get('/stats', [DirectorProfileController::class, 'stats'])->name('director.stats');
        Route::get('/add', [DirectorProfileController::class, 'add'])->name('director.add');
        Route::post('/store', [DirectorProfileController::class, 'store'])->name('director.store');
        Route::post('/commit', [DirectorProfileController::class, 'commit'])->name('director.commit');
    });
    Route::group(['prefix' => 'commission-profile'], function () {
        Route::get('/check-user/{kpi}/{user}', [CommissionController::class, 'check_user'])->name('commission.check_user');
        Route::post('/check-user/{kpi}/{user}', [CommissionController::class, 'check_user_store'])->name('commission.check_user_store');
        Route::get('/check-user-edit/{kpi}/{user}', [CommissionController::class, 'check_user_edit'])->name('commission.check_user_edit');

        Route::post('/update-criteria-score', [CommissionController::class, 'updateCriteriaScore'])->name('commission.update_criteria_score');
        Route::post('/update-comments', [CommissionController::class, 'updateComments'])->name('commission.update_comments');

        Route::get('/employee-list', [CommissionController::class, 'employeeList'])->name('commission.employee.list');

        Route::get('/user-band-scores/{id}', [CommissionController::class, 'scoresList'])->name('commission.band_scores.list');
        Route::post('/create-band-score/{id}/{user}', [CommissionController::class, 'createBandScore'])->name('commission.create.band_score');
    });
    Route::group(['prefix' => 'employee-profile'], function () {
        Route::get('/list', [EmployeeProfileController::class, 'index'])->name('profile.list');
        Route::get('/add', [EmployeeProfileController::class, 'add'])->name('profile.add');
        Route::get('/create', [EmployeeProfileController::class, 'create'])->name('profile.create');
        Route::post('/commit', [EmployeeProfileController::class, 'commit'])->name('profile.commit');
        Route::post('/store', [EmployeeProfileController::class, 'store'])->name('profile.store');

        Route::post('/kpi-save', [ EmployeeProfileController::class, 'KpiSave' ])->name('employee.kpi.save');

        Route::get('/kpi/dashboard', [EmployeeProfileController::class, 'index'])->name('kpi.dashboard');
        Route::get('/kpi/{kpiId}', [EmployeeProfileController::class, 'show'])->name('kpi.detail');
    });
    Route::group(['prefix' => 'bugalter'], function () {
        Route::get('/list', [BugalterController::class, 'index'])->name('bugalter.list');
        Route::get('/add', [BugalterController::class, 'add'])->name('bugalter.add');
        Route::get('/check', [BugalterController::class, 'check'])->name('bugalter.check');
        Route::post('/store', [BugalterController::class, 'store'])->name('bugalter.store');
        Route::get('/distribution/{id}', [BugalterController::class, 'distribution'])->name('bugalter.distribution');
        Route::post('/update/{id}', [BugalterController::class, 'update'])->name('bugalter.update');
        Route::get('/edit/{id}', [BugalterController::class, 'edit'])->name('bugalter.edit');
        Route::get('/select', [BugalterController::class, 'select'])->name('bugalter.select');
        Route::get('/export',[BugalterController::class, 'get_summa'])->name('bugalter.export');
        Route::get('/calculate',[BugalterController::class, 'calculate'])->name('bugalter.calculate');
    });

    Route::resource('kpis', KpiController::class);
    Route::resource('working-kpis', WorkingKpiController::class);
    Route::get('/working-kpis/user/{userId}', [WorkingKpiController::class, 'getUserKPIs'])->name('user-working-kpis.user');

    Route::post('/tasks/store', [TaskController::class, 'store']);
    Route::post('/tasks/update/{id}', [TaskController::class, 'update']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    Route::post('/tasks/{id}/ai-score', [TaskController::class, 'aiScore']);

    Route::get('/user-kpis', [UserKPIController::class, 'index'])->name('user-kpis.index');
    Route::get('/user-kpis/user/{userId}', [UserKPIController::class, 'getUserKPIs'])->name('user-kpis.user');
    Route::post('/user-kpis', [UserKPIController::class, 'store'])->name('user-kpis.store');
    Route::put('/user-kpis/{id}', [UserKPIController::class, 'update'])->name('user-kpis.update');
    Route::delete('/user-kpis/{id}', [UserKPIController::class, 'destroy'])->name('user-kpis.destroy');
    Route::get('/kpis/category/{categoryId}', [UserKPIController::class, 'getKPIsByCategory'])->name('kpis.by-category');


    Route::get('/user-kpi/{kpiId}/check-completion', [EmployeeKpiController::class, 'checkCompletion']);
    Route::post('/user-kpi/complete', [EmployeeKpiController::class, 'completeKpi']);

    Route::group(['prefix' => 'employee'], function () {
        // Users listing
        Route::get('/users', [EmployeeKpiController::class, 'index'])->name('employee.kpis.users');
        // User KPIs management
        Route::get('/users/{user}/kpis', [EmployeeKpiController::class, 'showKpis'])->name('employee.kpis');
        Route::post('/user-kpis/toggle', [EmployeeKpiController::class, 'toggle'])->name('user-kpi.toggle');
    });

    Route::get('/api/user-kpi-data/{userId}/{kpiId}',[CommissionController::class, 'getUserKpiData'])->name('user-kpi-data');
});


Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/relevant-users', [RelevantUserController::class, 'index'])->name('admin.relevant-users.index');
    Route::post('/relevant-users/assign', [RelevantUserController::class, 'assign'])->name('admin.relevant-users.assign');
});

Route::delete('/admin/relevant-users/{id}', [RelevantUserController::class, 'destroy'])->name('admin.relevant-users.destroy');
Route::get('/admin/relevant-users/{id}/edit', [RelevantUserController::class, 'edit'])->name('admin.relevant-users.edit');
Route::put('/admin/relevant-users/{id}', [RelevantUserController::class, 'update'])->name('admin.relevant-users.update');



Route::middleware(['auth'])->group(function () {
    Route::get('/boss/employees', [EmployeeController::class, 'index'])->name('boss.employees');
    Route::get('/boss/employees/{user}', [EmployeeController::class, 'show'])->name('boss.employee.show');

    Route::get('/departments', [DepartmentKpiController::class, 'index'])->name('kpi.departments');
    Route::get('/departments/{id}', [DepartmentKpiController::class, 'departmentDetail'])->name('kpi.department.detail');
    Route::get('/departments/users/{user}', [DepartmentKpiController::class, 'usersShow'])->name('department.user.detail');
});




