<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Admin\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Admin\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\GovernorateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Support\RoutePermissions;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:admin')
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        //Route::get('register', [RegisteredUserController::class, 'create'])
            //->name('register');

        //Route::post('register', [RegisteredUserController::class, 'store']);
        // Registration is disabled for admins via UI — redirect to login
        Route::get('register', function () {
            return redirect()->route('admin.login');
        })->name('register');

        Route::post('register', function () {
            return redirect()->route('admin.login');
        });

        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
            ->name('password.request');

        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
            ->name('password.email');

        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
            ->name('password.reset');

        Route::post('reset-password', [NewPasswordController::class, 'store'])
            ->name('password.store');
});

Route::middleware('auth:admin')
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

                // Dashboard
        Route::get('/dashboard', fn () => Inertia('Dashboard'))
            ->name('dashboard')
            ->middleware(RoutePermissions::can('dashboard.view'));

        // Profile
        Route::get('/profile', fn () => Inertia('Others/UserProfile'))
            ->name('profile')
            ->middleware(RoutePermissions::can('profile.view'));

        // Governorates
        Route::resource('governorates', GovernorateController::class)
            ->names('governorates');

        Route::patch('governorates/{id}/activate', [GovernorateController::class, 'activate'])
            ->name('governorates.activate')
            ->middleware(RoutePermissions::can('governorates.update'));

        Route::patch('governorates/{id}/deactivate', [GovernorateController::class, 'deactivate'])
            ->name('governorates.deactivate')
            ->middleware(RoutePermissions::can('governorates.update'));

        // Districts
        Route::resource('districts', DistrictController::class)
            ->names('districts');

        Route::patch('districts/{id}/activate', [DistrictController::class, 'activate'])
            ->name('districts.activate')
            ->middleware(RoutePermissions::can('districts.update'));

        Route::patch('districts/{id}/deactivate', [DistrictController::class, 'deactivate'])
            ->name('districts.deactivate')
            ->middleware(RoutePermissions::can('districts.update'));

        // Areas
        Route::resource('areas', AreaController::class)
            ->names('areas');

        Route::patch('areas/{id}/activate', [AreaController::class, 'activate'])
            ->name('areas.activate')
            ->middleware(RoutePermissions::can('areas.update'));

        Route::patch('areas/{id}/deactivate', [AreaController::class, 'deactivate'])
            ->name('areas.deactivate')
            ->middleware(RoutePermissions::can('areas.update'));

        // Users
        Route::resource('users', UserController::class)
            ->names('users');

        // Admins (managers of the system)
        Route::resource('admins', AdminController::class)
            ->names('admins');
        Route::patch('admins/{admin}/activate', [AdminController::class, 'activate'])
            ->name('admins.activate')
            ->middleware(RoutePermissions::can('admins.update'));

        Route::patch('admins/{admin}/deactivate', [AdminController::class, 'deactivate'])
            ->name('admins.deactivate')
            ->middleware(RoutePermissions::can('admins.update'));

        Route::patch('users/{id}/activate', [UserController::class, 'activate'])
            ->name('users.activate')
            ->middleware(RoutePermissions::can('users.update'));

        Route::patch('users/{id}/deactivate', [UserController::class, 'deactivate'])
            ->name('users.deactivate')
            ->middleware(RoutePermissions::can('users.update'));

        // Roles
        Route::resource('roles', RoleController::class)
            ->names('roles');

        Route::patch('roles/{id}/activate', [RoleController::class, 'activate'])
            ->name('roles.activate')
            ->middleware(RoutePermissions::can('roles.update'));

        Route::patch('roles/{id}/deactivate', [RoleController::class, 'deactivate'])
            ->name('roles.deactivate')
            ->middleware(RoutePermissions::can('roles.update'));

        // Permissions
        Route::get('permissions', [PermissionController::class, 'index'])
            ->name('permissions.index');

        // Activity Log
        Route::resource('activitylogs', ActivityLogController::class)
            ->only(['index', 'show' , 'destroy'])
            ->names('activitylogs');


        Route::get('verify-email', EmailVerificationPromptController::class)
            ->name('verification.notice');

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
            ->name('password.confirm');

        Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
});
