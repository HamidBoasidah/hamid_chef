<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\GovernorateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Support\RoutePermissions;
use App\Http\Controllers\LocaleController;


Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', fn () => Inertia('Dashboard'))
        ->name('dashboard')
        ->middleware(RoutePermissions::can('dashboard.view'));

    // Profile
    Route::get('/profile', fn () => Inertia('Others/UserProfile'))
        ->name('admin.profile')
        ->middleware(RoutePermissions::can('profile.view'));

    // Governorates
    Route::resource('governorates', GovernorateController::class)
        ->names('admin.governorates');

    Route::patch('governorates/{id}/activate', [GovernorateController::class, 'activate'])
        ->name('admin.governorates.activate')
        ->middleware(RoutePermissions::can('governorates.update'));

    Route::patch('governorates/{id}/deactivate', [GovernorateController::class, 'deactivate'])
        ->name('admin.governorates.deactivate')
        ->middleware(RoutePermissions::can('governorates.update'));

    // Districts
    Route::resource('districts', DistrictController::class)
        ->names('admin.districts');

    Route::patch('districts/{id}/activate', [DistrictController::class, 'activate'])
        ->name('admin.districts.activate')
        ->middleware(RoutePermissions::can('districts.update'));

    Route::patch('districts/{id}/deactivate', [DistrictController::class, 'deactivate'])
        ->name('admin.districts.deactivate')
        ->middleware(RoutePermissions::can('districts.update'));

    // Areas
    Route::resource('areas', AreaController::class)
        ->names('admin.areas');

    Route::patch('areas/{id}/activate', [AreaController::class, 'activate'])
        ->name('admin.areas.activate')
        ->middleware(RoutePermissions::can('areas.update'));

    Route::patch('areas/{id}/deactivate', [AreaController::class, 'deactivate'])
        ->name('admin.areas.deactivate')
        ->middleware(RoutePermissions::can('areas.update'));

    // Users
    Route::resource('users', UserController::class)
        ->names('admin.users');

    Route::patch('users/{id}/activate', [UserController::class, 'activate'])
        ->name('admin.users.activate')
        ->middleware(RoutePermissions::can('users.update'));

    Route::patch('users/{id}/deactivate', [UserController::class, 'deactivate'])
        ->name('admin.users.deactivate')
        ->middleware(RoutePermissions::can('users.update'));

    // Roles
    Route::resource('roles', RoleController::class)
        ->names('admin.roles');

    Route::patch('roles/{id}/activate', [RoleController::class, 'activate'])
        ->name('admin.roles.activate')
        ->middleware(RoutePermissions::can('roles.update'));

    Route::patch('roles/{id}/deactivate', [RoleController::class, 'deactivate'])
        ->name('admin.roles.deactivate')
        ->middleware(RoutePermissions::can('roles.update'));

    // Permissions
    Route::get('permissions', [PermissionController::class, 'index'])
        ->name('admin.permissions.index');

    // Activity Log
    Route::resource('activitylogs', ActivityLogController::class)
        ->only(['index', 'show' , 'destroy'])
        ->names('admin.activitylogs');
});

// روابط مصادقة لوحة التحكم (بدون حماية)
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/locale', LocaleController::class)->name('locale.set')->middleware('throttle:10,1');

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
