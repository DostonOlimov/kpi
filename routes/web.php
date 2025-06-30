<?php

use App\Http\Controllers\CommissionController;
use App\Http\Controllers\KomissionController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\EmployeeDaysController;
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

// Route::group([
//     'namespace'  => 'App\Http\Controllers\Admin',
//     'prefix'     => 'admin',
//     'middleware' => ['auth'],
// ], function () {
//     Route::resource('user', 'UserController');
//     Route::resource('role', 'RoleController');
//     Route::resource('permission', 'PermissionController');
// });

Route::get('login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post('authenticate', [\App\Http\Controllers\AuthController::class, 'authenticate'])->name('authenticate');
Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::get('/home', [\App\Http\Controllers\DashboardController::class, 'index'])->name('home');

Route::post('/change-year', [\App\Http\Controllers\SessionController::class, 'changeYear']);
Route::post('/change-month', [\App\Http\Controllers\SessionController::class, 'changeMonth']);


Route::middleware('auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::group(['prefix' => 'users'], function () {
        Route::get('/list', [\App\Http\Controllers\EmployeesController::class, 'index'])->name('employees.list');
        Route::post('/add', [\App\Http\Controllers\EmployeesController::class, 'add'])->name('employees.add');
        Route::post('/store', [\App\Http\Controllers\EmployeesController::class, 'store'])->name('employees.store');
        Route::get('/edit/{id}', [\App\Http\Controllers\EmployeesController::class, 'edit'])->name('employees.edit');
        Route::put('/update/{id}', [\App\Http\Controllers\EmployeesController::class, 'update'])->name('employees.update');
        Route::post('/delete/{id}', [\App\Http\Controllers\EmployeesController::class, 'delete'])->name('employees.delete');
        Route::delete('/destroy/{id}', [\App\Http\Controllers\EmployeesController::class, 'destroy'])->name('employees.destroy');
        Route::get('/show/{id}', [\App\Http\Controllers\EmployeesController::class, 'show'])->name('employees.show');
        Route::get('/create', [\App\Http\Controllers\EmployeesController::class, 'create'])->name('employees.create');

//        Route::get('/index', [\App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');

    });
  //  Route::resource('users', \App\Http\Controllers\EmployeesController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('works', WorkController::class);
    Route::resource('month', MonthController::class);
    Route::resource('days', EmployeeDaysController::class);
    Route::group(['prefix' => 'days'], function () {
        Route::get('/createday/{id}/{month_id}/{year}', [\App\Http\Controllers\EmployeeDaysController::class, 'createday'])->name('days.createday');
        Route::get('/list/{month_id}/{year}', [\App\Http\Controllers\EmployeeDaysController::class, 'list'])->name('days.list');
        Route::get('/select', [\App\Http\Controllers\EmployeeDaysController::class, 'select'])->name('days.select');
        Route::post('/store2', [\App\Http\Controllers\EmployeeDaysController::class, 'store2'])->name('days.store2');
    });
    Route::group(['prefix' => 'director-profile'], function () {
        Route::get('/list', [\App\Http\Controllers\DirectorProfileController::class, 'index'])->name('director.list');
        Route::get('/check-user/{employee}', [\App\Http\Controllers\DirectorProfileController::class, 'check_user'])->name('director.check_user');
        Route::get('/employees', [\App\Http\Controllers\DirectorProfileController::class, 'employees'])->name('director.employees');
        Route::get('/add', [\App\Http\Controllers\DirectorProfileController::class, 'add'])->name('director.add');
        Route::post('/store', [\App\Http\Controllers\DirectorProfileController::class, 'store'])->name('director.store');
        Route::post('/commit', [\App\Http\Controllers\DirectorProfileController::class, 'commit'])->name('director.commit');
        Route::get('/delete/{id}', [\App\Http\Controllers\DirectorProfileController::class, 'delete'])->name('director.delete');

    });
    Route::group(['prefix' => 'commission-profile'], function () {
        Route::get('/list', [\App\Http\Controllers\KomissionController::class, 'index'])->name('commission.list');
        Route::get('/add/{id}', [\App\Http\Controllers\KomissionController::class, 'add'])->name('commission.add');
        Route::get('/edit/{id}/{month_id}/{year}', [\App\Http\Controllers\KomissionController::class, 'edit'])->name('commission.edit');
        Route::post('/store', [\App\Http\Controllers\KomissionController::class, 'store'])->name('commission.store');
        Route::post('/store2', [\App\Http\Controllers\KomissionController::class, 'store2'])->name('commission.store2');
        Route::post('/update/{id}', [\App\Http\Controllers\KomissionController::class, 'update'])->name('commission.update');
        Route::get('/get-file', [\App\Http\Controllers\KomissionController::class, 'getFile'])->name('commission.file');
        Route::post('/ball', [ \App\Http\Controllers\KomissionController::class, 'AddBall' ])->name('commission.ball');
        Route::get('/ball.edit/{id}', [ \App\Http\Controllers\KomissionController::class, 'BallEdit' ])->name('commission.ball.edit');
        Route::get('/calculate/{id}/{month_id}', [\App\Http\Controllers\KomissionController::class, 'calculate'])->name('commission.calculate');
        Route::post('/upload', [\App\Http\Controllers\KomissionController::class, 'upload'])->name('commission.upload');
        Route::get('/download/{id}', [\App\Http\Controllers\KomissionController::class, 'download'])->name('commission.download');
        Route::get('/section', [\App\Http\Controllers\KomissionController::class, 'section'])->name('commission.section');

        Route::get('/text', [\App\Http\Controllers\KomissionController::class, 'text'])->name('commission.text');

        Route::post('/tasks/{task}/comment', [CommissionController::class, 'addComment'])->name('commission.task.comment');
        Route::post('/kpi/{child}/score', [CommissionController::class, 'scoreKPI'])->name('commission.kpi.score');
    });
    Route::group(['prefix' => 'employee-profile'], function () {
        Route::get('/list', [\App\Http\Controllers\EmployeeProfileController::class, 'index'])->name('profile.list');
        Route::get('/add', [\App\Http\Controllers\EmployeeProfileController::class, 'add'])->name('profile.add');
        Route::get('/create', [\App\Http\Controllers\EmployeeProfileController::class, 'create'])->name('profile.create');
        Route::post('/commit', [\App\Http\Controllers\EmployeeProfileController::class, 'commit'])->name('profile.commit');
        Route::post('/store', [\App\Http\Controllers\EmployeeProfileController::class, 'store'])->name('profile.store');

        Route::post('/save', [\App\Http\Controllers\EmployeeProfileController::class, 'save'])->name('profile.save');
        Route::get('/delete/{id}', [\App\Http\Controllers\EmployeeProfileController::class, 'delete'])->name('profile.delete');
        Route::get('/create2', [\App\Http\Controllers\EmployeeProfileController::class, 'create2'])->name('profile.create2');
        Route::post('/warn', [\App\Http\Controllers\EmployeeProfileController::class, 'warn'])->name('profile.warn');
        Route::post('/save2', [\App\Http\Controllers\EmployeeProfileController::class, 'save2'])->name('profile.save2');
        Route::get('/upload', [ \App\Http\Controllers\EmployeeProfileController::class, 'upload' ])->name('profile.upload');
        Route::get('/download/{id}', [\App\Http\Controllers\EmployeeProfileController::class, 'download'])->name('profile.download');
        Route::post('/image.store', [ \App\Http\Controllers\EmployeeProfileController::class, 'ImageStore' ])->name('profile.image.store');

        Route::post('/kpi-save', [ \App\Http\Controllers\EmployeeProfileController::class, 'KpiSave' ])->name('employee.kpi.save');
    });
    Route::group(['prefix' => 'bugalter'], function () {
        Route::get('/list', [\App\Http\Controllers\BugalterController::class, 'index'])->name('bugalter.list');
        Route::get('/add', [\App\Http\Controllers\BugalterController::class, 'add'])->name('bugalter.add');
        Route::get('/check', [\App\Http\Controllers\BugalterController::class, 'check'])->name('bugalter.check');
        Route::post('/store', [\App\Http\Controllers\BugalterController::class, 'store'])->name('bugalter.store');
        Route::get('/distribution/{id}', [\App\Http\Controllers\BugalterController::class, 'distribution'])->name('bugalter.distribution');
        Route::post('/update/{id}', [\App\Http\Controllers\BugalterController::class, 'update'])->name('bugalter.update');
        Route::get('/edit/{id}', [\App\Http\Controllers\BugalterController::class, 'edit'])->name('bugalter.edit');
        Route::get('/select', [\App\Http\Controllers\BugalterController::class, 'select'])->name('bugalter.select');
        Route::get('/export',[\App\Http\Controllers\BugalterController::class, 'get_summa'])->name('bugalter.export');
        Route::get('/calculate/{id}',[\App\Http\Controllers\BugalterController::class, 'calculate'])->name('bugalter.calculate');
    });

    Route::resource('kpis', KpiController::class);

    Route::post('/tasks/store', [TaskController::class, 'store']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
});




