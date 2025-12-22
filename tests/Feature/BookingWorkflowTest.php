<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Chef;
use App\Models\ChefService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingWorkflowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $customer;
    protected Chef $chef;
    protected ChefService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->customer = User::factory()->create();
        $this->chef = Chef::factory()->create();
        $this->service = ChefService::factory()->create([
            'chef_id' => $this->chef->id,
            'service_type' => 'hourly',
            'hourly_rate' => 150.00,
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_can_create_a_booking_successfully()
    {
        Sanctum::actingAs($this->customer);

        $bookingData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '14:00',
            'hours_count' => 3,
            'number_of_guests' => 4,
            'service_type' => 'hourly',
            'unit_price' => 150.00,
            'total_amount' => 450.00,
            'notes' => 'Test booking'
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Booking created successfully'
                ]);

        $this->assertDatabaseHas('bookings', [
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'date' => $bookingData['date'],
            'start_time' => $bookingData['start_time'],
            'hours_count' => $bookingData['hours_count'],
            'booking_status' => 'pending'
        ]);
    }

    /** @test */
    public function it_prevents_overlapping_bookings()
    {
        Sanctum::actingAs($this->customer);

        // Create first booking
        $existingBooking = Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '14:00:00',
            'hours_count' => 3,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        // Try to create overlapping booking
        $conflictingData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '15:00', // Overlaps with existing booking
            'hours_count' => 2,
            'number_of_guests' => 2,
            'service_type' => 'hourly',
            'unit_price' => 150.00,
            'total_amount' => 300.00
        ];

        $response = $this->postJson('/api/bookings', $conflictingData);

        $response->assertStatus(409)
                ->assertJson([
                    'success' => false,
                    'message' => 'Booking conflicts detected'
                ]);
    }

    /** @test */
    public function it_enforces_minimum_time_gap()
    {
        Sanctum::actingAs($this->customer);

        // Create first booking ending at 16:00
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '14:00:00',
            'hours_count' => 2, // Ends at 16:00
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        // Try to create booking starting at 17:00 (only 1 hour gap)
        $gapViolationData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '17:00', // Only 1 hour gap
            'hours_count' => 2,
            'number_of_guests' => 2,
            'service_type' => 'hourly',
            'unit_price' => 150.00,
            'total_amount' => 300.00
        ];

        $response = $this->postJson('/api/bookings', $gapViolationData);

        $response->assertStatus(409)
                ->assertJson([
                    'success' => false,
                    'message' => 'Time gap requirements not met'
                ]);
    }

    /** @test */
    public function it_allows_booking_with_sufficient_gap()
    {
        Sanctum::actingAs($this->customer);

        // Create first booking ending at 16:00
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '14:00:00',
            'hours_count' => 2, // Ends at 16:00
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        // Create booking starting at 18:00 (2 hour gap)
        $validData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '18:00', // 2 hour gap
            'hours_count' => 2,
            'number_of_guests' => 2,
            'service_type' => 'hourly',
            'unit_price' => 150.00,
            'total_amount' => 300.00
        ];

        $response = $this->postJson('/api/bookings', $validData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Booking created successfully'
                ]);
    }

    /** @test */
    public function it_can_check_chef_availability()
    {
        Sanctum::actingAs($this->customer);

        $response = $this->getJson("/api/chefs/{$this->chef->id}/availability?" . http_build_query([
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '14:00',
            'hours_count' => 3
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
    public function it_can_update_booking_with_conflict_validation()
    {
        Sanctum::actingAs($this->customer);

        $booking = Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '14:00:00',
            'hours_count' => 2,
            'booking_status' => 'pending'
        ]);

        $updateData = [
            'hours_count' => 3,
            'number_of_guests' => 6
        ];

        $response = $this->putJson("/api/bookings/{$booking->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'hours_count' => 3,
            'number_of_guests' => 6
        ]);
    }

    /** @test */
    public function it_can_cancel_booking()
    {
        Sanctum::actingAs($this->customer);

        $booking = Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'booking_status' => 'pending'
        ]);

        $response = $this->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Booking cancelled successfully'
                ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_status' => 'cancelled_by_customer',
            'is_active' => false
        ]);
    }

    /** @test */
    public function it_validates_booking_data()
    {
        Sanctum::actingAs($this->customer);

        $invalidData = [
            'chef_id' => 999, // Non-existent chef
            'date' => now()->subDays(1)->format('Y-m-d'), // Past date
            'start_time' => '25:00', // Invalid time
            'hours_count' => 0, // Invalid hours
        ];

        $response = $this->postJson('/api/bookings', $invalidData);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors'
                ]);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $bookingData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '14:00',
            'hours_count' => 3
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(401);
    }

    /** @test */
    public function admin_can_create_booking_through_admin_panel()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin'); // Assuming Spatie roles

        $this->actingAs($admin);

        $bookingData = [
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '14:00',
            'hours_count' => 3,
            'number_of_guests' => 4,
            'service_type' => 'hourly',
            'unit_price' => 150.00,
            'total_amount' => 450.00,
            'booking_status' => 'accepted'
        ];

        $response = $this->post('/admin/bookings', $bookingData);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('bookings', [
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'booking_status' => 'accepted'
        ]);
    }

    /** @test */
    public function it_handles_concurrent_booking_requests()
    {
        Sanctum::actingAs($this->customer);

        $bookingData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '14:00',
            'hours_count' => 3,
            'number_of_guests' => 4,
            'service_type' => 'hourly',
            'unit_price' => 150.00,
            'total_amount' => 450.00
        ];

        // Simulate concurrent requests
        $responses = [];
        for ($i = 0; $i < 3; $i++) {
            $responses[] = $this->postJson('/api/bookings', $bookingData);
        }

        // Only one should succeed
        $successCount = 0;
        $conflictCount = 0;

        foreach ($responses as $response) {
            if ($response->status() === 201) {
                $successCount++;
            } elseif ($response->status() === 409) {
                $conflictCount++;
            }
        }

        $this->assertEquals(1, $successCount, 'Only one booking should succeed');
        $this->assertEquals(2, $conflictCount, 'Two bookings should be rejected due to conflict');
    }

    /** @test */
    public function it_respects_operating_hours()
    {
        Sanctum::actingAs($this->customer);

        // Try to book outside operating hours (before 8 AM)
        $earlyBookingData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '07:00', // Before 8 AM
            'hours_count' => 2,
            'number_of_guests' => 2,
            'service_type' => 'hourly',
            'unit_price' => 150.00,
            'total_amount' => 300.00
        ];

        $response = $this->postJson('/api/bookings', $earlyBookingData);

        $response->assertStatus(422)
                ->assertJsonPath('errors.start_time.0', 'Start time cannot be before 8:00 AM');

        // Try to book ending after 10 PM
        $lateBookingData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '21:00', // Ends at 23:00 (11 PM)
            'hours_count' => 2,
            'number_of_guests' => 2,
            'service_type' => 'hourly',
            'unit_price' => 150.00,
            'total_amount' => 300.00
        ];

        $response = $this->postJson('/api/bookings', $lateBookingData);

        $response->assertStatus(422)
                ->assertJsonPath('errors.hours_count.0', 'Booking cannot end after 10:00 PM');
    }
}