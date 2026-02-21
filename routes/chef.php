<?php

/**
 * Chef Panel Routes
 *
 * This file contains all routes for the Chef control panel.
 * All routes are prefixed with '/chef' and use the 'chef' guard.
 */

use App\Http\Controllers\Chef\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Chef\Auth\PasswordResetLinkController;
use App\Http\Controllers\Chef\Auth\NewPasswordController;
use App\Http\Controllers\Chef\Auth\PasswordController;
use App\Http\Controllers\Chef\DashboardController;
use App\Http\Controllers\Chef\ChefServiceController;
use App\Http\Controllers\Chef\BookingController;
use App\Http\Controllers\Chef\ProfileController;
use App\Http\Controllers\Chef\VacationController;
use App\Http\Controllers\Chef\WorkingHourController;
use App\Http\Controllers\Chef\WalletController;
use App\Http\Controllers\Chef\KycController;
use App\Http\Controllers\Chef\AddressController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes (Not Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware('guest:chef')
    ->prefix('chef')
    ->as('chef.')
    ->group(function () {
        // Login
        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        // Forgot Password
        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
            ->name('password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
            ->name('password.email');

        // Reset Password
        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
            ->name('password.reset');
        Route::post('reset-password', [NewPasswordController::class, 'store'])
            ->name('password.store');
    });

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:chef', 'ensure.chef.type'])
    ->prefix('chef')
    ->as('chef.')
    ->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])
            ->name('index');
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Services - using 'service' as route parameter to match controller
        Route::get('services', [ChefServiceController::class, 'index'])
            ->name('services.index');
        Route::get('services/create', [ChefServiceController::class, 'create'])
            ->name('services.create');
        Route::post('services', [ChefServiceController::class, 'store'])
            ->name('services.store');
        Route::get('services/{service}', [ChefServiceController::class, 'show'])
            ->name('services.show');
        Route::get('services/{service}/edit', [ChefServiceController::class, 'edit'])
            ->name('services.edit');
        Route::put('services/{service}', [ChefServiceController::class, 'update'])
            ->name('services.update');
        Route::delete('services/{service}', [ChefServiceController::class, 'destroy'])
            ->name('services.destroy');
        Route::patch('services/{id}/activate', [ChefServiceController::class, 'activate'])
            ->name('services.activate');
        Route::patch('services/{id}/deactivate', [ChefServiceController::class, 'deactivate'])
            ->name('services.deactivate');

        // Bookings
        Route::get('bookings', [BookingController::class, 'index'])
            ->name('bookings.index');
        Route::get('bookings/{booking}', [BookingController::class, 'show'])
            ->name('bookings.show');
        Route::patch('bookings/{id}/accept', [BookingController::class, 'accept'])
            ->name('bookings.accept');
        Route::patch('bookings/{id}/reject', [BookingController::class, 'reject'])
            ->name('bookings.reject');
        Route::patch('bookings/{id}/complete', [BookingController::class, 'complete'])
            ->name('bookings.complete');

        // Profile
        Route::get('profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');
        Route::patch('profile', [ProfileController::class, 'update'])
            ->name('profile.update');
        Route::put('password', [PasswordController::class, 'update'])
            ->name('password.update');

        // Vacations
        Route::get('vacations', [VacationController::class, 'index'])
            ->name('vacations.index');
        Route::get('vacations/create', [VacationController::class, 'create'])
            ->name('vacations.create');
        Route::post('vacations', [VacationController::class, 'store'])
            ->name('vacations.store');
        Route::get('vacations/{vacation}/edit', [VacationController::class, 'edit'])
            ->name('vacations.edit');
        Route::put('vacations/{vacation}', [VacationController::class, 'update'])
            ->name('vacations.update');
        Route::delete('vacations/{vacation}', [VacationController::class, 'destroy'])
            ->name('vacations.destroy');

        // Working Hours
        Route::get('working-hours', [WorkingHourController::class, 'index'])
            ->name('working-hours.index');
        Route::put('working-hours', [WorkingHourController::class, 'update'])
            ->name('working-hours.update');

        // Wallet
        Route::get('wallet', [WalletController::class, 'index'])
            ->name('wallet.index');
        Route::get('wallet/transactions', [WalletController::class, 'transactions'])
            ->name('wallet.transactions');
        Route::post('wallet/withdraw', [WalletController::class, 'withdraw'])
            ->name('wallet.withdraw');

        // Reports
        Route::get('reports/bookings', [\App\Http\Controllers\Chef\ReportController::class, 'bookings'])
            ->name('reports.bookings');
        Route::get('reports/earnings', [\App\Http\Controllers\Chef\ReportController::class, 'earnings'])
            ->name('reports.earnings');
        Route::get('reports/services', [\App\Http\Controllers\Chef\ReportController::class, 'services'])
            ->name('reports.services');
        Route::get('reports/export', [\App\Http\Controllers\Chef\ReportController::class, 'export'])
            ->name('reports.export');
        Route::get('reports/export-excel', [\App\Http\Controllers\Chef\ReportController::class, 'exportExcel'])
            ->name('reports.export.excel');
        Route::get('reports/export-pdf', [\App\Http\Controllers\Chef\ReportController::class, 'exportPdf'])
            ->name('reports.export.pdf');

        // KYC
        Route::get('kyc', [KycController::class, 'index'])
            ->name('kyc.index');
        Route::get('kyc/create', [KycController::class, 'create'])
            ->name('kyc.create');
        Route::post('kyc', [KycController::class, 'store'])
            ->name('kyc.store');
        Route::get('kyc/{kyc}', [KycController::class, 'show'])
            ->name('kyc.show');
        Route::get('kyc/{kyc}/edit', [KycController::class, 'edit'])
            ->name('kyc.edit');
        Route::put('kyc/{kyc}', [KycController::class, 'update'])
            ->name('kyc.update');
        Route::delete('kyc/{kyc}', [KycController::class, 'destroy'])
            ->name('kyc.destroy');
        Route::get('kyc/{kyc}/download/{type}', [KycController::class, 'download'])
            ->name('kyc.download');

        // Addresses
        Route::get('addresses', [AddressController::class, 'index'])
            ->name('addresses.index');
        Route::get('addresses/create', [AddressController::class, 'create'])
            ->name('addresses.create');
        Route::post('addresses', [AddressController::class, 'store'])
            ->name('addresses.store');
        Route::get('addresses/{address}/edit', [AddressController::class, 'edit'])
            ->name('addresses.edit');
        Route::put('addresses/{address}', [AddressController::class, 'update'])
            ->name('addresses.update');
        Route::delete('addresses/{address}', [AddressController::class, 'destroy'])
            ->name('addresses.destroy');
        Route::patch('addresses/{address}/default', [AddressController::class, 'setDefault'])
            ->name('addresses.default');

        // Logout
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
    });
