<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BloodInventoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BloodComponentForecastingController;
use App\Http\Controllers\RequestInquisitionSlipController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\UsageHistoryController;
use App\Http\Controllers\ForecastingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForecastController;

// SARIMA CONTROLLER
Route::post('/sarima-predict', [ForecastController::class, 'predict']);

Route::get('/sarima-predict', [ForecastController::class, 'getDailyUsage']);

// Dashboard Routes
Route::get('/dashboard-data', [DashboardController::class, 'getDashboardData']);

// Forecast Routes
Route::get('/forecast', [ForecastingController::class, 'forecastUsage']);
Route::get('/forecast/component', [BloodComponentForecastingController::class, 'forecastComponentUsage']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Blood Inventory Routes
Route::resource('blood-inventory', BloodInventoryController::class);
Route::get('/blood-inventory', [BloodInventoryController::class, 'index']);
Route::post('/blood-inventory', [BloodInventoryController::class, 'store']);
Route::get('/blood-inventory/{id}', [BloodInventoryController::class, 'show']);
Route::put('/blood-inventory/{id}', [BloodInventoryController::class, 'update']);
Route::delete('/blood-inventory/{id}', [BloodInventoryController::class, 'destroy']);
Route::post('/blood-inventory/delete-multiple', [BloodInventoryController::class, 'deleteMultiple']);
Route::post('/blood-inventory/update-and-log', [BloodInventoryController::class, 'updateAndLog']);

// Expired Blood Routes
Route::get('/expired-blood', [BloodInventoryController::class, 'getExpiredBlood']);
Route::post('/expired-blood/delete', [BloodInventoryController::class, 'deleteExpired']);


// Create Add-User Routes
Route::resource('users', UserController::class);
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::post('/users/delete-multiple', [UserController::class, 'deleteMultiple']);

// User Role Routes

// Fetch User Data
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'user']);

// RIS
Route::post('/request-inquisition-slip', [RequestInquisitionSlipController::class, 'store']);
Route::get('/blood-requests', [RequestInquisitionSlipController::class, 'index']);
Route::put('/blood-requests/{id}', [RequestInquisitionSlipController::class, 'update']);
Route::delete('/blood-requests/{id}', [RequestInquisitionSlipController::class, 'destroy']);
Route::post('/blood-requests/delete-multiple', [RequestInquisitionSlipController::class, 'deleteMultiple']);
Route::get('/blood-requests/{id}/requisition-items', [RequestInquisitionSlipController::class, 'showRequisitionItems']);
Route::put('/blood-requests/{id}/accept', [BloodRequestController::class, 'accept']);

// Usage History Routes
Route::get('/usage-history', [UsageHistoryController::class, 'index']);
