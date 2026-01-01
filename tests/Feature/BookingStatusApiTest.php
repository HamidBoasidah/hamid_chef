<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Chef;
use App\Models\ChefService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingStatusApiTest extends TestCase
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
    public function customer_can_cancel_own_booking()
    {
        Sanctum::actingAs($this->customer);

        $booking = Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'booking_status' => 'pending',
            'is_active' => true,
        ]);

        $response = $this->postJson("/api/bookings/{$booking->id}/cancel-by-customer");
        $response->assertStatus(200);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_status' => 'cancelled_by_customer',
            'is_active' => false,
        ]);
    }

    /** @test */
    public function chef_can_accept_booking()
    {
        Sanctum::actingAs($this->chef->user);

        $booking = Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'booking_status' => 'pending',
        ]);

        $response = $this->postJson("/api/chef/bookings/{$booking->id}/accept");
        $response->assertStatus(200);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_status' => 'accepted',
        ]);
    }

    /** @test */
    public function chef_can_reject_booking()
    {
        Sanctum::actingAs($this->chef->user);

        $booking = Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'booking_status' => 'pending',
            'is_active' => true,
        ]);

        $response = $this->postJson("/api/chef/bookings/{$booking->id}/reject");
        $response->assertStatus(200);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_status' => 'rejected',
            'is_active' => false,
        ]);
    }

    /** @test */
    public function chef_cannot_reject_when_status_is_not_pending()
    {
        Sanctum::actingAs($this->chef->user);

        $booking = Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'booking_status' => 'accepted',
            'is_active' => true,
        ]);

        $response = $this->postJson("/api/chef/bookings/{$booking->id}/reject");
        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ])
                 ->assertJsonPath('errors.booking_status.0', 'الحالة الحالية لا تسمح بالرفض');
    }

    /** @test */
    public function chef_cannot_accept_when_status_is_not_pending()
    {
        Sanctum::actingAs($this->chef->user);

        $booking = Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'booking_status' => 'accepted',
            'is_active' => true,
        ]);

        $response = $this->postJson("/api/chef/bookings/{$booking->id}/accept");
        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ])
                 ->assertJsonPath('errors.booking_status.0', 'الحالة الحالية لا تسمح بالقبول');
    }

    /** @test */
    public function chef_cannot_cancel_when_status_is_not_accepted()
    {
        Sanctum::actingAs($this->chef->user);

        $booking = Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'booking_status' => 'pending',
            'is_active' => true,
        ]);

        $response = $this->postJson("/api/chef/bookings/{$booking->id}/cancel");
        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ])
                 ->assertJsonPath('errors.booking_status.0', 'الحالة الحالية لا تسمح بالإلغاء من الطاهي');
    }

    /** @test */
    public function chef_cannot_complete_when_status_is_not_accepted()
    {
        Sanctum::actingAs($this->chef->user);

        $booking = Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'booking_status' => 'pending',
            'is_active' => true,
        ]);

        $response = $this->postJson("/api/chef/bookings/{$booking->id}/complete");
        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ])
                 ->assertJsonPath('errors.booking_status.0', 'الحالة الحالية لا تسمح بالإكمال');
    }

    /** @test */
    public function chef_can_cancel_booking()
    {
        Sanctum::actingAs($this->chef->user);

        $booking = Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'booking_status' => 'accepted',
            'is_active' => true,
        ]);

        $response = $this->postJson("/api/chef/bookings/{$booking->id}/cancel");
        $response->assertStatus(200);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_status' => 'cancelled_by_chef',
            'is_active' => false,
        ]);
    }

    /** @test */
    public function chef_can_complete_booking()
    {
        Sanctum::actingAs($this->chef->user);

        $booking = Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'booking_status' => 'accepted',
            'is_active' => true,
        ]);

        $response = $this->postJson("/api/chef/bookings/{$booking->id}/complete");
        $response->assertStatus(200);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_status' => 'completed',
        ]);
    }

    /** @test */
    public function customer_cannot_cancel_when_status_is_not_pending_or_accepted()
    {
        Sanctum::actingAs($this->customer);

        $booking = Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'booking_status' => 'completed',
            'is_active' => true,
        ]);

        $response = $this->postJson("/api/bookings/{$booking->id}/cancel-by-customer");
        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ])
                 ->assertJsonPath('errors.booking_status.0', 'الحالة الحالية لا تسمح بالإلغاء');
    }
}
