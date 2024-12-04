<?php

use App\Http\Controllers\EmployeeController;
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

Route::get('/employee', [EmployeeController::class, 'index'])->name('employees');
Route::get('/employee/jabatan', [EmployeeController::class, 'jabatan'])->name('employees.jabatan');

Route::post('/employee', [EmployeeController::class, 'store'])->name('employees.store');
Route::post('/employee/excel', [EmployeeController::class, 'storeExcel'])->name('employees.store.excel');

Route::patch('/employee/{id}', [EmployeeController::class, 'update'])->name('employees.update');
Route::delete('/employee/{id}', [EmployeeController::class, 'delete'])->name('employees.delete');
