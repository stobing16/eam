<?php

use App\Http\Controllers\Api\Android\AssetOpnameSingleController;
use App\Http\Controllers\Api\Master\AssetTypeController;
use App\Http\Controllers\Api\Master\BrandController;
use App\Http\Controllers\Api\Master\MainGroupController;
use App\Http\Controllers\Api\Master\ModelAssetController;
use App\Http\Controllers\Api\Transaction\AssetController;
use App\Http\Controllers\Api\Transaction\OpnameController;
use App\Http\Controllers\Api\Report\AssetLogHistoryController;
use App\Http\Controllers\Api\Report\BarcodePrintingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarcodeCollectingController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SubLocationController;
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
});

// Transaksi
Route::prefix('transaksi')->group(function () {
    Route::get('/assets', [AssetController::class, 'index']);
    Route::get('/assets/{id}/check-out', [AssetController::class, 'getAssetCheckout']);
    Route::get('/assets/{id}/check-in', [AssetController::class, 'getAssetCheckin']);
    Route::get('/assets/list', [AssetController::class, 'getFormDataList']);

    Route::post('/assets', [AssetController::class, 'saveAsset']);
    Route::patch('/assets/{id}', [AssetController::class, 'updateAsset']);

    Route::post('/assets/checkin', [AssetController::class, 'checkIn']);
    Route::post('/assets/checkout', [AssetController::class, 'checkOut']);

    Route::get('/opname', [OpnameController::class, 'index']);
    Route::get('/opname/loc', [OpnameController::class, 'getLocationList']);
    Route::get('/opname/{id}', [OpnameController::class, 'details']);
    Route::post('/opname', [OpnameController::class, 'saveOpname']);
    Route::patch('/opname/{id}', [OpnameController::class, 'updateOpname']);
});

Route::prefix('report')->group(function() {
    Route::get('/barcode-collecting', [BarcodeCollectingController::class, 'index']);
    Route::get('/asset-log-history', [AssetLogHistoryController::class, 'index']);
    Route::get('/barcode-printing', [BarcodePrintingController::class, 'index']);
});

// MASTER

// --- ASSET HIRARKI ---
Route::get('/main-group', [MainGroupController::class, 'index']);
Route::post('/main-group', [MainGroupController::class, 'store']);
Route::patch('/main-group/{id}', [MainGroupController::class, 'update']);
Route::delete('/main-group/{id}', [MainGroupController::class, 'delete']);

Route::get('/asset-type/{code}', [AssetTypeController::class, 'index']);
Route::post('/asset-type', [AssetTypeController::class, 'store']);
Route::patch('/asset-type/{id}', [AssetTypeController::class, 'update']);
Route::delete('/asset-type/{id}', [AssetTypeController::class, 'delete']);

Route::get('/brand/{code}', [BrandController::class, 'index']);
Route::post('/brand', [BrandController::class, 'store']);
Route::patch('/brand/{id}', [BrandController::class, 'update']);
Route::delete('/brand/{id}', [BrandController::class, 'delete']);

Route::get('/model-asset/{code}', [ModelAssetController::class, 'index']);
Route::post('/model-asset', [ModelAssetController::class, 'store']);
Route::patch('/model-asset/{id}', [ModelAssetController::class, 'update']);
Route::delete('/model-asset/{id}', [ModelAssetController::class, 'delete']);

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

Route::get('/sub-location', [SubLocationController::class, 'index'])->name('sub-locations');
Route::post('/sub-location', [SubLocationController::class, 'store'])->name('sub-locations.store');
Route::patch('/sub-location/{id}', [SubLocationController::class, 'update'])->name('sub-locations.update');
Route::delete('/sub-location/{id}', [SubLocationController::class, 'delete'])->name('sub-locations.delete');

Route::get('/project', [ProjectController::class, 'index'])->name('projects');
Route::post('/project', [ProjectController::class, 'store'])->name('projects.store');
Route::patch('/project/{id}', [ProjectController::class, 'update'])->name('projects.update');
Route::delete('/project/{id}', [ProjectController::class, 'delete'])->name('projects.delete');

Route::get('/project', [ProjectController::class, 'index'])->name('projects');
Route::post('/project', [ProjectController::class, 'store'])->name('projects.store');
Route::patch('/project/{id}', [ProjectController::class, 'update'])->name('projects.update');
Route::delete('/project/{id}', [ProjectController::class, 'delete'])->name('projects.delete');

Route::get('/android/opname-list', [AssetOpnameSingleController::class, 'getOpnameOrderAndroidList']);
Route::get('/android/opname-list/barcode', [AssetOpnameSingleController::class, 'getOpnameOrderAndroidDetailList']);
Route::post('/android/opname-list', [AssetOpnameSingleController::class, 'saveAsset']);

