<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public routes for viewing chefs (list and single) — available to guests
Route::get('chefs', [App\Http\Controllers\Api\ChefController::class, 'index']);
Route::get('chefs/{chef}', [App\Http\Controllers\Api\ChefController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('addresses', App\Http\Controllers\Api\AddressController::class);
    Route::post('addresses/{address}/activate', [App\Http\Controllers\Api\AddressController::class, 'activate']);
    Route::post('addresses/{address}/deactivate', [App\Http\Controllers\Api\AddressController::class, 'deactivate']);
    Route::post('addresses/{address}/set-default', [App\Http\Controllers\Api\AddressController::class, 'setDefault']);

});

Route::group(['prefix' => 'chef', 'middleware' => ['auth:sanctum', 'user_role:chef']], function () {

    // KYCs API
    Route::apiResource('kycs', App\Http\Controllers\Api\KycController::class);
    Route::get('kycs/{kyc}/document/view', [App\Http\Controllers\Api\KycController::class, 'viewDocument']);
    Route::get('kycs/{kyc}/document/download', [App\Http\Controllers\Api\KycController::class, 'downloadDocument']);

    // Chefs API (protected actions only; index/show are public)
    Route::apiResource('chefs', App\Http\Controllers\Api\ChefController::class)->except(['index', 'show']);
    Route::post('chefs/{chef}/activate', [App\Http\Controllers\Api\ChefController::class, 'activate']);
    Route::post('chefs/{chef}/deactivate', [App\Http\Controllers\Api\ChefController::class, 'deactivate']);
});

Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/logout-all-devices', [App\Http\Controllers\Api\AuthController::class, 'logoutFromAllDevices'])->middleware('auth:sanctum');
Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me'])->middleware('auth:sanctum');