<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BloodInventoryController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::resource('blood-inventory', BloodInventoryController::class);
Route::get('/blood-inventory', [BloodInventoryController::class, 'index']);
Route::post('/blood-inventory', [BloodInventoryController::class, 'store']);
Route::get('/blood-inventory/{id}', [BloodInventoryController::class, 'show']);
Route::put('/blood-inventory/{id}', [BloodInventoryController::class, 'update']);
Route::delete('/blood-inventory/{id}', [BloodInventoryController::class, 'destroy']);
