<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BloodInventoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

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

// Create Add-User Routes
Route::resource('users', UserController::class);
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::post('/users/delete-multiple', [UserController::class, 'deleteMultiple']);


// Permissions Routes
