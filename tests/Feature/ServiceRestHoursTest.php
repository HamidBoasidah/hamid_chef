<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Chef;
use App\Models\ChefService;
use App\Models\User;
use App\Services\BookingConflictService;
use App\DTOs\BookingDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ServiceRestHoursTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $customer;
    protected Chef $chef;
    protected ChefService $serviceWith2HoursRest;
    protected ChefService $serviceWith4HoursRest;
    protected BookingConflictService $conflictService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customer = User::factory()->create();
        $this->chef = Chef::factory()->create(['is_active' => true]);

        // Service with 2 hours rest
        $this->serviceWith2HoursRest = ChefService::factory()->create([
            'chef_id' => $this->chef->id,
            'name' => 'طبخ منزلي',
            'service_type' => 'hourly',
            'hourly_rate' => 100.00,
            'min_hours' => 2,
            'rest_hours_required' => 2,
            'is_active' => true
        ]);

        // Service with 4 hours rest
        $this->serviceWith4HoursRest = ChefService::factory()->create([
            'chef_id' => $this->chef->id,
            'name' => 'طبخ حفلات كبيرة',
            'service_type' => 'hourly',
            'hourly_rate' => 300.00,
            'min_hours' => 4,
            'rest_hours_required' => 4,
            'is_active' => true
        ]);

        $this->conflictService = app(BookingConflictService::class);
    }

    /** @test */
    public function it_uses_service_specific_rest_hours_for_new_booking()
    {
        Sanctum::actingAs($this->customer);

        // Create existing booking with 4-hour rest service (10:00-12:00, blocked until 16:00)
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith4HoursRest->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'hours_count' => 2,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        // Try to book at 14:00 (only 2 hours after existing booking ends)
        // Should fail because existing booking needs 4 hours rest
        $bookingData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith2HoursRest->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '14:00',
            'hours_count' => 2,
            'number_of_guests' => 2,
            'service_type' => 'hourly',
            'unit_price' => 100.00,
            'total_amount' => 200.00
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(409)
            ->assertJson([
                'success' => false
            ]);
    }

    /** @test */
    public function it_allows_booking_after_service_rest_hours_complete()
    {
        Sanctum::actingAs($this->customer);

        // Create existing booking with 4-hour rest service (10:00-12:00, blocked until 16:00)
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith4HoursRest->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'hours_count' => 2,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        // Book at 16:00 (4 hours after existing booking ends) - should succeed
        $bookingData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith2HoursRest->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '16:00',
            'hours_count' => 2,
            'number_of_guests' => 2,
            'service_type' => 'hourly',
            'unit_price' => 100.00,
            'total_amount' => 200.00
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'تم إنشاء الحجز بنجاح'
            ]);
    }

    /** @test */
    public function it_validates_new_booking_rest_hours_before_existing()
    {
        Sanctum::actingAs($this->customer);

        // Create existing booking at 15:00
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith2HoursRest->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '15:00:00',
            'hours_count' => 2,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        // Try to book 10:00-12:00 with 4-hour rest service
        // New booking ends at 12:00, needs 4 hours rest, so blocked until 16:00
        // This conflicts with existing booking at 15:00 (only 3 hours gap, needs 4)
        $bookingData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith4HoursRest->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '10:00',
            'hours_count' => 2,
            'number_of_guests' => 2,
            'service_type' => 'hourly',
            'unit_price' => 300.00,
            'total_amount' => 600.00
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(409);
    }

    /** @test */
    public function it_allows_booking_when_rest_hours_dont_overlap()
    {
        Sanctum::actingAs($this->customer);

        // Create existing booking at 18:00
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith2HoursRest->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '18:00:00',
            'hours_count' => 2,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        // Book 10:00-12:00 with 4-hour rest (blocked until 16:00)
        // Existing booking starts at 18:00, so there's 2 hours gap - should succeed
        $bookingData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith4HoursRest->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '10:00',
            'hours_count' => 2,
            'number_of_guests' => 2,
            'service_type' => 'hourly',
            'unit_price' => 300.00,
            'total_amount' => 600.00
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(201);
    }

    /** @test */
    public function it_uses_default_rest_hours_when_service_has_none()
    {
        // Create service with default rest_hours_required (2 hours - from database default)
        $serviceDefaultRest = ChefService::factory()->create([
            'chef_id' => $this->chef->id,
            'name' => 'خدمة بساعات راحة افتراضية',
            'service_type' => 'hourly',
            'hourly_rate' => 50.00,
            'min_hours' => 1,
            // rest_hours_required will use database default (2)
            'is_active' => true
        ]);

        Sanctum::actingAs($this->customer);

        // Create booking with this service
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'chef_service_id' => $serviceDefaultRest->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'hours_count' => 2,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        // Try to book at 13:00 (only 1 hour after booking ends at 12:00)
        // Should fail because default rest hours is 2
        $bookingData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $serviceDefaultRest->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '13:00',
            'hours_count' => 2,
            'number_of_guests' => 2,
            'service_type' => 'hourly',
            'unit_price' => 50.00,
            'total_amount' => 100.00
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(409);
    }

    /** @test */
    public function check_availability_uses_service_rest_hours()
    {
        Sanctum::actingAs($this->customer);

        // Create existing booking with 4-hour rest
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith4HoursRest->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'hours_count' => 2,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        $date = now()->addDays(1)->format('Y-m-d');

        // Check availability at 14:00 with 2-hour rest service (should be unavailable)
        $response = $this->getJson("/api/chefs/{$this->chef->id}/availability?" . http_build_query([
            'date' => $date,
            'start_time' => '14:00',
            'hours_count' => 2,
            'chef_service_id' => $this->serviceWith2HoursRest->id
        ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'available' => false
                ]
            ]);

        // Check availability at 16:00 (should be available)
        $response = $this->getJson("/api/chefs/{$this->chef->id}/availability?" . http_build_query([
            'date' => $date,
            'start_time' => '16:00',
            'hours_count' => 2,
            'chef_service_id' => $this->serviceWith2HoursRest->id
        ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'available' => true
                ]
            ]);
    }

    /** @test */
    public function conflict_response_includes_rest_hours_info()
    {
        Sanctum::actingAs($this->customer);

        // Create existing booking with 4-hour rest
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith4HoursRest->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'hours_count' => 2,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        // Try conflicting booking
        $bookingData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith2HoursRest->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '14:00',
            'hours_count' => 2,
            'number_of_guests' => 2,
            'service_type' => 'hourly',
            'unit_price' => 100.00,
            'total_amount' => 200.00
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(409)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
                'conflicting_bookings'
            ]);

        // Check that conflicting_bookings contains rest hours info
        $conflictingBookings = $response->json('conflicting_bookings');
        if (!empty($conflictingBookings)) {
            $this->assertArrayHasKey('required_rest_hours', $conflictingBookings[0]);
        }
    }

    /** @test */
    public function it_handles_multiple_bookings_with_different_rest_hours()
    {
        Sanctum::actingAs($this->customer);

        $date = now()->addDays(1)->format('Y-m-d');

        // Create first booking: 09:00-11:00 with 2-hour rest (blocked until 13:00)
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith2HoursRest->id,
            'date' => $date,
            'start_time' => '09:00:00',
            'hours_count' => 2,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        // Create second booking: 17:00-19:00 with 4-hour rest
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith4HoursRest->id,
            'date' => $date,
            'start_time' => '17:00:00',
            'hours_count' => 2,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        // Try to book 13:00-15:00 with 2-hour rest (blocked until 17:00)
        // This should succeed because:
        // - First booking blocked until 13:00, we start at 13:00 ✓
        // - Our booking ends at 15:00, needs 2 hours rest (until 17:00)
        // - Second booking starts at 17:00 ✓
        $bookingData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith2HoursRest->id,
            'date' => $date,
            'start_time' => '13:00',
            'hours_count' => 2,
            'number_of_guests' => 2,
            'service_type' => 'hourly',
            'unit_price' => 100.00,
            'total_amount' => 200.00
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(201);
    }

    /** @test */
    public function it_rejects_booking_that_violates_rest_hours_in_middle()
    {
        Sanctum::actingAs($this->customer);

        $date = now()->addDays(1)->format('Y-m-d');

        // Create first booking: 09:00-11:00 with 2-hour rest (blocked until 13:00)
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith2HoursRest->id,
            'date' => $date,
            'start_time' => '09:00:00',
            'hours_count' => 2,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        // Create second booking: 16:00-18:00
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith2HoursRest->id,
            'date' => $date,
            'start_time' => '16:00:00',
            'hours_count' => 2,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        // Try to book 13:00-15:00 with 4-hour rest (blocked until 19:00)
        // This should fail because our rest period (until 19:00) overlaps with second booking (16:00)
        $bookingData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWith4HoursRest->id,
            'date' => $date,
            'start_time' => '13:00',
            'hours_count' => 2,
            'number_of_guests' => 2,
            'service_type' => 'hourly',
            'unit_price' => 300.00,
            'total_amount' => 600.00
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(409);
    }
}
