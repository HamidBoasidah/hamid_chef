<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\WithdrawalMethod;
use App\Models\Chef;
use App\Models\ChefService;
use App\Models\ChefGallery;
use App\Models\ChefServiceImage;
use App\Models\ChefCategory;
use App\Models\ChefServiceTag;
use App\Models\Address;
use App\Models\Kyc;
use App\Models\Booking;
use App\Models\BookingTransaction;
use App\Models\ChefWallet;
use App\Models\ChefWalletTransaction;
use App\Models\ChefWithdrawalRequest;
use App\Models\ChefRating;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Seed foundational data
        Category::factory()->count(8)->create();
        Tag::factory()->count(12)->create();
        WithdrawalMethod::factory()->count(3)->create();

        // Create users: customers and chefs
        $customers = User::factory()->count(24)->customer()->create();
        $chefUsers = User::factory()->count(6)->chef()->create();

        // Create chefs for chef users
        $chefs = collect();
        foreach ($chefUsers as $u) {
            $chefs->push(Chef::factory()->create(['user_id' => $u->id]));
        }

        // Addresses and KYC for some users
        foreach ($customers->take(10) as $c) {
            Address::factory()->create(['user_id' => $c->id]);
        }
        foreach ($chefUsers as $u) {
            Kyc::factory()->create(['user_id' => $u->id]);
            Address::factory()->create(['user_id' => $u->id]);
        }

        // For each chef, create categories, gallery, services, wallet
        foreach ($chefs as $chef) {
            ChefGallery::factory()->count(3)->create(['chef_id' => $chef->id]);
            ChefService::factory()->count(3)->create(['chef_id' => $chef->id])->each(function ($service) use ($chef) {
                ChefServiceImage::factory()->count(2)->create(['chef_service_id' => $service->id]);
                // attach existing tags and categories randomly via pivot factories
                ChefServiceTag::factory()->create(['chef_service_id' => $service->id, 'tag_id' => Tag::inRandomOrder()->first()->id]);
            });

            // Link cuisine categories
            ChefCategory::factory()->create(['chef_id' => $chef->id, 'cuisine_id' => Category::inRandomOrder()->first()->id]);

            // Wallet and transactions
            ChefWallet::factory()->create(['chef_id' => $chef->id]);
            ChefWalletTransaction::factory()->count(3)->create(['chef_id' => $chef->id]);

            // Withdrawal requests
            ChefWithdrawalRequest::factory()->count(1)->create(['chef_id' => $chef->id, 'withdrawal_method_id' => WithdrawalMethod::inRandomOrder()->first()->id]);
        }

        // Create bookings for some services by random customers
        $services = ChefService::all();
        foreach ($services as $service) {
            $customersSample = $customers->random(min(4, $customers->count()));
            foreach ($customersSample as $cust) {
                $booking = Booking::factory()->create([
                    'customer_id' => $cust->id,
                    'chef_id' => $service->chef_id,
                    'chef_service_id' => $service->id,
                    'address_id' => Address::where('user_id', $cust->id)->inRandomOrder()->first()->id ?? Address::factory()->create(['user_id' => $cust->id])->id,
                ]);

                BookingTransaction::factory()->create(['booking_id' => $booking->id]);

                // Optionally add rating for some bookings
                if (rand(0,1)) {
                    ChefRating::factory()->create(['booking_id' => $booking->id, 'customer_id' => $booking->customer_id, 'chef_id' => $booking->chef_id]);
                }
            }
        }
    }
}
