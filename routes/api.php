<?php

use App\Http\Controllers\Api\Transaction\AssetController;
use App\Http\Controllers\Api\Transaction\OpnameController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SupplierController;
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

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/assets', [AssetController::class, 'saveAsset'])->name('assets.save');
    Route::patch('/assets', [AssetController::class, 'updateAsset'])->name('assets.update');
    Route::post('/assets/checkin', [AssetController::class, 'checkIn'])->name('assets.checkin');
    Route::post('/assets/checkout', [AssetController::class, 'checkOut'])->name('assets.checkout');

    Route::get('/opname', [OpnameController::class, 'index'])->name('opname');
    Route::get('/opname/{id}', [OpnameController::class, 'details'])->name('opname.details');
    Route::post('/opname', [OpnameController::class, 'saveOpname'])->name('opname.save');
    Route::patch('/opname/{id}', [OpnameController::class, 'updateOpname'])->name('opname.update');
});

Route::get('/employee', [EmployeeController::class, 'index'])->name('employees');
Route::get('/employee/jabatan', [EmployeeController::class, 'jabatan'])->name('employees.jabatan');
Route::post('/employee', [EmployeeController::class, 'store'])->name('employees.store');
Route::post('/employee/excel', [EmployeeController::class, 'storeExcel'])->name('employees.store.excel');
Route::patch('/employee/{id}', [EmployeeController::class, 'update'])->name('employees.update');
Route::delete('/employee/{id}', [EmployeeController::class, 'delete'])->name('employees.delete');

Route::get('/company', [CompanyController::class, 'index'])->name('companies');
Route::post('/company', [CompanyController::class, 'store'])->name('companies.store');
Route::patch('/company/{id}', [CompanyController::class, 'update'])->name('companies.update');
Route::delete('/company/{id}', [CompanyController::class, 'delete'])->name('companies.delete');

Route::get('/supplier', [SupplierController::class, 'index'])->name('suppliers');
Route::post('/supplier', [SupplierController::class, 'store'])->name('suppliers.store');
Route::patch('/supplier/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
Route::delete('/supplier/{id}', [SupplierController::class, 'delete'])->name('suppliers.delete');

Route::get('/location', [LocationController::class, 'index'])->name('locations');
Route::post('/location', [LocationController::class, 'store'])->name('locations.store');
Route::patch('/location/{id}', [LocationController::class, 'update'])->name('locations.update');
Route::delete('/location/{id}', [LocationController::class, 'delete'])->name('locations.delete');

Route::get('/sub-location', [LocationController::class, 'index'])->name('sub-locations');
Route::post('/sub-location', [LocationController::class, 'store'])->name('sub-locations.store');
Route::patch('/sub-location/{id}', [LocationController::class, 'update'])->name('sub-locations.update');
Route::delete('/sub-location/{id}', [LocationController::class, 'delete'])->name('sub-locations.delete');

Route::get('/project', [ProjectController::class, 'index'])->name('projects');
Route::post('/project', [ProjectController::class, 'store'])->name('projects.store');
Route::patch('/project/{id}', [ProjectController::class, 'update'])->name('projects.update');
Route::delete('/project/{id}', [ProjectController::class, 'delete'])->name('projects.delete');

Route::get('/project', [ProjectController::class, 'index'])->name('projects');
Route::post('/project', [ProjectController::class, 'store'])->name('projects.store');
Route::patch('/project/{id}', [ProjectController::class, 'update'])->name('projects.update');
Route::delete('/project/{id}', [ProjectController::class, 'delete'])->name('projects.delete');
