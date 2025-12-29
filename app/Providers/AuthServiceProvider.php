<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\Chef;
use App\Models\Booking;
use App\Models\ChefService;
use App\Policies\AddressPolicy;
use App\Policies\ChefPolicy;
use App\Policies\BookingPolicy;
use App\Policies\ChefServicePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Address::class => AddressPolicy::class,
        Chef::class => ChefPolicy::class,
        Booking::class => BookingPolicy::class,
        ChefService::class => ChefServicePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}