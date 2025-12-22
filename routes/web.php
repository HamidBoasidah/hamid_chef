<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocaleController;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware(['auth'])
    ->group(function () {

});

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', function () {
        return redirect()->route('admin.bookings.index');
    })->name('home');
    
    Route::get('/dashboard', function () {
        return redirect()->route('admin.bookings.index');
    })->name('dashboard');
    
    // Booking Management Routes
    Route::resource('bookings', App\Http\Controllers\Admin\BookingController::class);
    
    // AJAX Routes for Booking Management
    Route::post('bookings/bulk-update', [App\Http\Controllers\Admin\BookingController::class, 'bulkUpdate'])
        ->name('bookings.bulk-update');
    
    Route::get('bookings/chef/{chef}/availability', [App\Http\Controllers\Admin\BookingController::class, 'checkAvailability'])
        ->name('bookings.check-availability');
    
    Route::get('bookings/chef/{chef}/bookings', [App\Http\Controllers\Admin\BookingController::class, 'getChefBookings'])
        ->name('bookings.chef-bookings');
});

// روابط مصادقة لوحة التحكم (بدون حماية)
//Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
//Route::post('login', [AuthController::class, 'login'])->name('login');
//Route::post('logout', [AuthController::class, 'logout'])->name('logout');
//Route::post('/locale', LocaleController::class)->name('locale.set')->middleware('throttle:10,1');

// --- BREEZE MERGED CONTENT START ---
// Note: Duplicate imports and routes commented out to prevent conflicts.

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
// use Illuminate\Support\Facades\Route; // Already imported
// use Inertia\Inertia; // Already imported

/*
// Conflict: You already have a root route '/' defined above.
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Conflict: You already have a '/dashboard' route defined above.
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// --- BREEZE MERGED CONTENT END ---
