<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Chef;
use App\Models\ChefService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected Chef $chef;
    protected ChefService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
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
    public function it_handles_multiple_availability_checks_efficiently()
    {
        Sanctum::actingAs($this->customer);

        // Create some existing bookings
        Booking::factory()->count(10)->create([
            'chef_id' => $this->chef->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        $startTime = microtime(true);

        // Perform multiple availability checks
        for ($i = 0; $i < 20; $i++) {
            $response = $this->getJson("/api/chefs/{$this->chef->id}/availability?" . http_build_query([
                'date' => now()->addDays(1)->format('Y-m-d'),
                'start_time' => sprintf('%02d:00', 8 + ($i % 12)),
                'hours_count' => 2
            ]));

            $response->assertStatus(200);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Should complete within reasonable time (adjust threshold as needed)
        $this->assertLessThan(5.0, $executionTime, 'Availability checks took too long: ' . $executionTime . ' seconds');
    }

    /** @test */
    public function it_handles_concurrent_booking_creation_efficiently()
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

        $startTime = microtime(true);

        // Simulate concurrent booking attempts
        $responses = [];
        for ($i = 0; $i < 10; $i++) {
            $responses[] = $this->postJson('/api/bookings', array_merge($bookingData, [
                'start_time' => sprintf('%02d:00', 8 + $i) // Different times to avoid conflicts
            ]));
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Check that all requests completed
        foreach ($responses as $response) {
            $this->assertContains($response->status(), [201, 409, 422]);
        }

        // Should complete within reasonable time
        $this->assertLessThan(10.0, $executionTime, 'Concurrent booking creation took too long: ' . $executionTime . ' seconds');
    }

    /** @test */
    public function it_efficiently_queries_chef_bookings_with_large_dataset()
    {
        Sanctum::actingAs($this->customer);

        // Create a large number of bookings for the chef
        Booking::factory()->count(100)->create([
            'chef_id' => $this->chef->id,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        $startTime = microtime(true);

        $response = $this->getJson("/api/chefs/{$this->chef->id}/bookings?" . http_build_query([
            'date' => now()->addDays(1)->format('Y-m-d')
        ]));

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $response->assertStatus(200);

        // Should complete within reasonable time even with large dataset
        $this->assertLessThan(2.0, $executionTime, 'Chef bookings query took too long: ' . $executionTime . ' seconds');
    }

    /** @test */
    public function it_efficiently_handles_conflict_detection_with_many_existing_bookings()
    {
        Sanctum::actingAs($this->customer);

        // Create many existing bookings for the same chef
        $date = now()->addDays(1)->format('Y-m-d');
        for ($hour = 8; $hour < 20; $hour += 3) {
            Booking::factory()->create([
                'chef_id' => $this->chef->id,
                'date' => $date,
                'start_time' => sprintf('%02d:00:00', $hour),
                'hours_count' => 2,
                'booking_status' => 'accepted',
                'is_active' => true
            ]);
        }

        $bookingData = [
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'date' => $date,
            'start_time' => '09:00', // Should conflict
            'hours_count' => 2,
            'number_of_guests' => 4,
            'service_type' => 'hourly',
            'unit_price' => 150.00,
            'total_amount' => 300.00
        ];

        $startTime = microtime(true);

        $response = $this->postJson('/api/bookings', $bookingData);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $response->assertStatus(409); // Should detect conflict

        // Conflict detection should be fast even with many existing bookings
        $this->assertLessThan(1.0, $executionTime, 'Conflict detection took too long: ' . $executionTime . ' seconds');
    }

    /** @test */
    public function it_efficiently_handles_database_locking()
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

        $startTime = microtime(true);

        // Test that locking doesn't cause significant delays
        $response = $this->postJson('/api/bookings', $bookingData);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $response->assertStatus(201);

        // Database locking should not cause significant delays
        $this->assertLessThan(3.0, $executionTime, 'Database locking caused excessive delay: ' . $executionTime . ' seconds');
    }

    /** @test */
    public function it_efficiently_handles_booking_list_pagination()
    {
        Sanctum::actingAs($this->customer);

        // Create many bookings for the customer
        Booking::factory()->count(50)->create([
            'customer_id' => $this->customer->id
        ]);

        $startTime = microtime(true);

        $response = $this->getJson('/api/bookings?per_page=15');

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'current_page',
                        'data',
                        'per_page',
                        'total'
                    ]
                ]);

        // Pagination should be efficient
        $this->assertLessThan(1.0, $executionTime, 'Booking list pagination took too long: ' . $executionTime . ' seconds');
    }

    /** @test */
    public function it_measures_memory_usage_during_conflict_detection()
    {
        Sanctum::actingAs($this->customer);

        // Create some existing bookings
        Booking::factory()->count(20)->create([
            'chef_id' => $this->chef->id,
            'date' => now()->addDays(1)->format('Y-m-d'),
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        $memoryBefore = memory_get_usage(true);

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

        $response = $this->postJson('/api/bookings', $bookingData);

        $memoryAfter = memory_get_usage(true);
        $memoryUsed = $memoryAfter - $memoryBefore;

        // Memory usage should be reasonable (adjust threshold as needed)
        $this->assertLessThan(10 * 1024 * 1024, $memoryUsed, 'Excessive memory usage: ' . ($memoryUsed / 1024 / 1024) . ' MB');
    }

    /** @test */
    public function it_tests_database_query_count_optimization()
    {
        Sanctum::actingAs($this->customer);

        // Enable query logging
        \DB::enableQueryLog();

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

        $response = $this->postJson('/api/bookings', $bookingData);

        $queries = \DB::getQueryLog();
        $queryCount = count($queries);

        $response->assertStatus(201);

        // Should not execute excessive queries (adjust threshold as needed)
        $this->assertLessThan(20, $queryCount, 'Too many database queries executed: ' . $queryCount);

        \DB::disableQueryLog();
    }
}