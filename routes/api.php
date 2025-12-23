<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public routes for viewing chefs (list and single) — available to guests
Route::get('chefs', [App\Http\Controllers\Api\ChefController::class, 'index']);
// Get chefs by category (public) — placed BEFORE the parameterized show route to avoid route shadowing
Route::get('chefs/by-category/{category}', [App\Http\Controllers\Api\ChefController::class, 'byCategory']);
Route::get('chefs/{chef}', [App\Http\Controllers\Api\ChefController::class, 'show']);
// Public routes for viewing chef services (list and single) — available to guests
Route::get('chef-services', [App\Http\Controllers\Api\ChefServiceController::class, 'index']);
Route::get('chef-services/{chefService}', [App\Http\Controllers\Api\ChefServiceController::class, 'show']);

// Public routes for viewing chef service equipment — available to guests
Route::get('chef-services/{serviceId}/equipment', [App\Http\Controllers\Api\ChefServiceController::class, 'getEquipment']);
Route::get('chef-service-equipment/{id}', [App\Http\Controllers\Api\ChefServiceController::class, 'showEquipment']);
// Public route for categories index
Route::get('categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);
Route::get('categories/{id}', [App\Http\Controllers\Api\CategoryController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('addresses', App\Http\Controllers\Api\AddressController::class);
    Route::post('addresses/{address}/activate', [App\Http\Controllers\Api\AddressController::class, 'activate']);
    Route::post('addresses/{address}/deactivate', [App\Http\Controllers\Api\AddressController::class, 'deactivate']);
    Route::post('addresses/{address}/set-default', [App\Http\Controllers\Api\AddressController::class, 'setDefault']);

    // Chef Service Ratings API Routes (only allow store, update, destroy via API)
    Route::apiResource('chef-service-ratings', App\Http\Controllers\Api\ChefServiceRatingController::class)->only(['store', 'update', 'destroy']);
    
    // Booking API Routes with Rate Limiting
    Route::middleware('App\Http\Middleware\BookingRateLimitMiddleware:general_api')->group(function () {
        Route::get('bookings', [App\Http\Controllers\Api\BookingController::class, 'index']);
        Route::get('bookings/{booking}', [App\Http\Controllers\Api\BookingController::class, 'show']);
        Route::put('bookings/{booking}', [App\Http\Controllers\Api\BookingController::class, 'update']);
        Route::delete('bookings/{booking}', [App\Http\Controllers\Api\BookingController::class, 'destroy']);
        Route::get('chefs/{chef}/bookings', [App\Http\Controllers\Api\BookingController::class, 'getChefBookings']);
    });
    
    // Booking Creation with Stricter Rate Limiting
    Route::middleware('App\Http\Middleware\BookingRateLimitMiddleware:booking_creation')->group(function () {
        Route::post('bookings', [App\Http\Controllers\Api\BookingController::class, 'store']);
        Route::post('bookings/validate', [App\Http\Controllers\Api\BookingController::class, 'validateBooking']);
    });
    
    // Availability Check with Higher Rate Limit
    Route::middleware('App\Http\Middleware\BookingRateLimitMiddleware:availability_check')->group(function () {
        Route::get('chefs/{chef}/availability', [App\Http\Controllers\Api\BookingController::class, 'checkAvailability']);
    });

    // Categories removed from protected routes (only index is exposed publicly)

    // Category Icons Management
    Route::post('categories/{id}/icon', [App\Http\Controllers\Api\CategoryController::class, 'uploadIcon']);
    Route::delete('categories/{id}/icon', [App\Http\Controllers\Api\CategoryController::class, 'removeIcon']);

});

// Public route for chef ratings (anyone can view)
Route::get('chefs/{chef}/ratings', [App\Http\Controllers\Api\ChefServiceRatingController::class, 'getChefRatings']);

Route::group(['prefix' => 'chef', 'middleware' => ['auth:sanctum', 'user_role:chef']], function () {

    // KYCs API
    Route::apiResource('kycs', App\Http\Controllers\Api\KycController::class);
    Route::get('kycs/{kyc}/document/view', [App\Http\Controllers\Api\KycController::class, 'viewDocument']);
    Route::get('kycs/{kyc}/document/download', [App\Http\Controllers\Api\KycController::class, 'downloadDocument']);

    // Chefs API (protected actions only; index/show are public)
    Route::apiResource('chefs', App\Http\Controllers\Api\ChefController::class)->except(['index', 'show']);
    Route::post('chefs/{chef}/activate', [App\Http\Controllers\Api\ChefController::class, 'activate']);
    Route::post('chefs/{chef}/deactivate', [App\Http\Controllers\Api\ChefController::class, 'deactivate']);

    // Chef Services API (protected actions only; index/show are public)
    Route::apiResource('chef-services', App\Http\Controllers\Api\ChefServiceController::class)->except(['index', 'show']);
    Route::post('chef-services/{chefService}/activate', [App\Http\Controllers\Api\ChefServiceController::class, 'activate']);
    Route::post('chef-services/{chefService}/deactivate', [App\Http\Controllers\Api\ChefServiceController::class, 'deactivate']);

    // Chef Service Equipment API (protected actions only; index/show are public)
    Route::post('chef-services/{serviceId}/equipment', [App\Http\Controllers\Api\ChefServiceController::class, 'storeEquipment']);
    Route::put('chef-service-equipment/{id}', [App\Http\Controllers\Api\ChefServiceController::class, 'updateEquipment']);
    Route::delete('chef-service-equipment/{id}', [App\Http\Controllers\Api\ChefServiceController::class, 'destroyEquipment']);
    Route::post('chef-service-equipment/bulk-manage', [App\Http\Controllers\Api\ChefServiceController::class, 'bulkManageEquipment']);
    Route::post('chef-service-equipment/copy-from-service', [App\Http\Controllers\Api\ChefServiceController::class, 'copyEquipmentFromService']);
});

Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/logout-all-devices', [App\Http\Controllers\Api\AuthController::class, 'logoutFromAllDevices'])->middleware('auth:sanctum');
Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me'])->middleware('auth:sanctum');