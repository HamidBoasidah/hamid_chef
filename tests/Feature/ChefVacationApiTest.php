<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Booking;
use App\Models\Chef;
use App\Models\ChefService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChefVacationApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected Chef $chef;
    protected ChefService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->customer()->create();
        $this->chef = Chef::factory()->create();
        $this->service = ChefService::factory()->for($this->chef, 'chef')->create([
            'service_type' => 'hourly',
            'hourly_rate' => 100.00,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function cannot_create_vacation_on_a_date_with_active_booking()
    {
        Sanctum::actingAs($this->chef->user);

        $date = Carbon::now()->addDays(2)->format('Y-m-d');

        // Ensure customer has an address and create an active booking for the chef on the same date
        $address = Address::factory()->create(['user_id' => $this->customer->id]);

        Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'address_id' => $address->id,
            'date' => $date,
            'booking_status' => 'accepted', // active per scopeActive
            'is_active' => true,
        ]);

        // Attempt to create a vacation on the same date
        $response = $this->postJson('/api/chef/vacations', [
            'date' => $date,
            'note' => 'Day off',
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ])
                 ->assertJsonPath('errors.date.0', 'لا يمكن إضافة إجازة لوجود حجز نشط في نفس اليوم');

        // Ensure no vacation was created
        $this->assertDatabaseCount('chef_vacations', 0);
    }

    /** @test */
    public function cannot_create_vacation_on_booked_date_when_sending_form_data()
    {
        Sanctum::actingAs($this->chef->user);

        $date = Carbon::now()->addDays(3)->format('Y-m-d');

        // Customer address and active booking
        $address = Address::factory()->create(['user_id' => $this->customer->id]);
        Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'address_id' => $address->id,
            'date' => $date,
            'booking_status' => 'accepted',
            'is_active' => true,
        ]);

        // Simulate multipart/form-data request
        $response = $this->post('/api/chef/vacations', [
            'date' => $date,
            'note' => 'Form Data Day off',
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'multipart/form-data',
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ])
                 ->assertJsonPath('errors.date.0', 'لا يمكن إضافة إجازة لوجود حجز نشط في نفس اليوم');

        $this->assertDatabaseCount('chef_vacations', 0);
    }
}
