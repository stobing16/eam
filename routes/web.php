<?php

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
    return redirect('/employee');
});
Route::get('/employee', [\App\Http\Controllers\HomeController::class, 'index'])->name('employees');
Route::get('/create', [\App\Http\Controllers\HomeController::class, 'create'])->name('employees.create');
Route::post('/create/employee', [\App\Http\Controllers\HomeController::class, 'store'])->name('employees.store');
