<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Chef;
use App\Models\ChefService;
use App\Models\ChefWorkingHour;
use App\Models\User;
use App\Services\ChefAvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;
use Tests\TestCase;

class ChefAvailabilityApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Chef $chef;
    protected ChefService $service;
    protected ChefService $serviceWithHighRestHours;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create chef
        $this->chef = Chef::factory()->create([
            'name' => 'Test Chef',
            'is_active' => true
        ]);

        // Create service with default rest hours (2)
        $this->service = ChefService::factory()->create([
            'chef_id' => $this->chef->id,
            'name' => 'طبخ منزلي',
            'service_type' => 'hourly',
            'hourly_rate' => 100.00,
            'min_hours' => 2,
            'rest_hours_required' => 2,
            'is_active' => true
        ]);

        // Create service with high rest hours (4)
        $this->serviceWithHighRestHours = ChefService::factory()->create([
            'chef_id' => $this->chef->id,
            'name' => 'طبخ حفلات',
            'service_type' => 'hourly',
            'hourly_rate' => 200.00,
            'min_hours' => 3,
            'rest_hours_required' => 4,
            'is_active' => true
        ]);

        // Create working hours for chef (Sunday to Thursday, 9:00-14:00 and 16:00-22:00)
        $workingDays = [0, 1, 2, 3, 4]; // Sunday to Thursday
        foreach ($workingDays as $day) {
            // Morning shift
            ChefWorkingHour::factory()->create([
                'chef_id' => $this->chef->id,
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '14:00:00',
                'is_active' => true
            ]);
            // Evening shift
            ChefWorkingHour::factory()->create([
                'chef_id' => $this->chef->id,
                'day_of_week' => $day,
                'start_time' => '16:00:00',
                'end_time' => '22:00:00',
                'is_active' => true
            ]);
        }
    }

    /** @test */
    public function it_returns_chef_availability_calendar()
    {
        $response = $this->getJson("/api/chefs/{$this->chef->id}/availability-calendar");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'تم جلب بيانات التوفر بنجاح'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'chef_id',
                    'chef_name',
                    'default_rest_hours',
                    'calendar_start_date',
                    'calendar_end_date',
                    'calendar' => [
                        'available_days',
                        'off_days',
                        'partially_booked_days',
                        'fully_booked_days'
                    ],
                    'selected_date',
                    'day_details',
                    'services'
                ]
            ]);
    }

    /** @test */
    public function it_returns_calendar_until_end_of_month()
    {
        // Test with a date in the middle of the month
        $testDate = Carbon::create(2026, 1, 15);
        Carbon::setTestNow($testDate);
        
        $response = $this->getJson("/api/chefs/{$this->chef->id}/availability-calendar?date=2026-01-15");

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertEquals('2026-01-15', $data['calendar_start_date']);
        $this->assertEquals('2026-01-15', $data['selected_date']);
        $this->assertEquals('2026-01-31', $data['calendar_end_date']); // End of January
        
        Carbon::setTestNow();
    }

    /** @test */
    public function it_ensures_minimum_10_days_when_near_end_of_month()
    {
        // Test with date near end of month (Jan 25 - only 7 days left)
        $response = $this->getJson("/api/chefs/{$this->chef->id}/availability-calendar?date=2026-01-25");

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        // Should go back to ensure at least 10 days
        // Jan 25 to Jan 31 = 7 days, need 3 more, so start from Jan 22
        $this->assertEquals('2026-01-22', $data['calendar_start_date']);
        $this->assertEquals('2026-01-25', $data['selected_date']); // Selected date stays the same
        $this->assertEquals('2026-01-31', $data['calendar_end_date']);
        
        // Verify we have at least 10 days
        $startDate = Carbon::parse($data['calendar_start_date']);
        $endDate = Carbon::parse($data['calendar_end_date']);
        $dayCount = $startDate->diffInDays($endDate) + 1;
        $this->assertGreaterThanOrEqual(10, $dayCount);
    }

    /** @test */
    public function it_handles_last_day_of_month()
    {
        // Test with Jan 30 (only 2 days left: 30 and 31)
        $response = $this->getJson("/api/chefs/{$this->chef->id}/availability-calendar?date=2026-01-30");

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        // Should go back 8 days to ensure 10 days total
        // Jan 30-31 = 2 days, need 8 more, so start from Jan 22
        $this->assertEquals('2026-01-22', $data['calendar_start_date']);
        $this->assertEquals('2026-01-30', $data['selected_date']);
        $this->assertEquals('2026-01-31', $data['calendar_end_date']);
    }

    /** @test */
    public function it_defaults_to_today_when_no_date_provided()
    {
        $response = $this->getJson("/api/chefs/{$this->chef->id}/availability-calendar");

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $today = now()->format('Y-m-d');
        $endOfMonth = now()->endOfMonth()->format('Y-m-d');
        
        // Selected date should be today
        $this->assertEquals($today, $data['selected_date']);
        
        // End date should be end of month
        $this->assertEquals($endOfMonth, $data['calendar_end_date']);
    }

    /** @test */
    public function it_returns_off_days_correctly()
    {
        // Friday (5) and Saturday (6) are off days
        $response = $this->getJson("/api/chefs/{$this->chef->id}/availability-calendar");

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $offDays = collect($data['calendar']['off_days']);
        
        // Check that off days exist
        $this->assertNotEmpty($offDays);
        
        // Verify off days have correct structure
        $offDays->each(function ($day) {
            $this->assertArrayHasKey('date', $day);
            $this->assertArrayHasKey('day_name', $day);
            $this->assertArrayHasKey('day_name_ar', $day);
        });
    }

    /** @test */
    public function it_returns_services_with_rest_hours()
    {
        $response = $this->getJson("/api/chefs/{$this->chef->id}/availability-calendar");

        $response->assertStatus(200);
        
        $services = $response->json('data.services');
        
        $this->assertCount(2, $services);
        
        // Check first service
        $service1 = collect($services)->firstWhere('id', $this->service->id);
        $this->assertEquals(2, $service1['rest_hours_required']);
        
        // Check second service with high rest hours
        $service2 = collect($services)->firstWhere('id', $this->serviceWithHighRestHours->id);
        $this->assertEquals(4, $service2['rest_hours_required']);
    }

    /** @test */
    public function it_returns_day_details_with_working_hours()
    {
        $response = $this->getJson("/api/chefs/{$this->chef->id}/availability-calendar");

        $response->assertStatus(200);
        
        $dayDetails = $response->json('data.day_details');
        
        // If it's a working day
        if ($dayDetails['is_working_day']) {
            $this->assertFalse($dayDetails['is_off_day']);
            $this->assertNotEmpty($dayDetails['working_hours']);
            $this->assertArrayHasKey('available_slots', $dayDetails);
        }
    }

    /** @test */
    public function it_returns_bookings_with_service_rest_hours()
    {
        // Create a booking with the high rest hours service
        $bookingDate = $this->getNextWorkingDay();
        
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWithHighRestHours->id,
            'date' => $bookingDate,
            'start_time' => '10:00:00',
            'hours_count' => 2,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        $response = $this->getJson("/api/chefs/{$this->chef->id}/availability-calendar?date={$bookingDate}");

        $response->assertStatus(200);
        
        $dayDetails = $response->json('data.day_details');
        $bookings = $dayDetails['bookings'];
        
        $this->assertCount(1, $bookings);
        
        $booking = $bookings[0];
        $this->assertEquals('10:00', $booking['start_time']);
        $this->assertEquals('12:00', $booking['end_time']);
        $this->assertEquals(2, $booking['hours_count']);
        $this->assertEquals(4, $booking['rest_hours_required']); // From service
        $this->assertEquals('16:00', $booking['blocked_until']); // 12:00 + 4 hours
        $this->assertEquals(6, $booking['total_blocked_hours']); // 2 + 4
    }

    /** @test */
    public function it_calculates_available_slots_considering_rest_hours()
    {
        $bookingDate = $this->getNextWorkingDay();
        
        // Create booking from 10:00-12:00 with 4 hours rest (blocked until 16:00)
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->serviceWithHighRestHours->id,
            'date' => $bookingDate,
            'start_time' => '10:00:00',
            'hours_count' => 2,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        $response = $this->getJson("/api/chefs/{$this->chef->id}/availability-calendar?date={$bookingDate}");

        $response->assertStatus(200);
        
        $availableSlots = $response->json('data.day_details.available_slots');
        
        // Should have slot from 09:00-10:00 (before booking)
        // And slot from 16:00-22:00 (after rest period ends)
        $this->assertNotEmpty($availableSlots);
        
        // Check that no slot starts between 10:00 and 16:00
        foreach ($availableSlots as $slot) {
            $startTime = $slot['start_time'];
            if ($startTime >= '10:00' && $startTime < '16:00') {
                $this->fail("Found available slot starting at {$startTime} which should be blocked");
            }
        }
    }

    /** @test */
    public function it_marks_partially_booked_days_correctly()
    {
        $bookingDate = $this->getNextWorkingDay();
        
        // Create one booking (partial day)
        Booking::factory()->create([
            'chef_id' => $this->chef->id,
            'chef_service_id' => $this->service->id,
            'date' => $bookingDate,
            'start_time' => '10:00:00',
            'hours_count' => 2,
            'booking_status' => 'accepted',
            'is_active' => true
        ]);

        $response = $this->getJson("/api/chefs/{$this->chef->id}/availability-calendar?date={$bookingDate}");

        $response->assertStatus(200);
        
        $partiallyBookedDays = $response->json('data.calendar.partially_booked_days');
        
        $dayFound = collect($partiallyBookedDays)->firstWhere('date', $bookingDate);
        $this->assertNotNull($dayFound);
        $this->assertEquals(1, $dayFound['bookings_count']);
        $this->assertGreaterThan(10, $dayFound['availability_percentage']);
    }

    /** @test */
    public function it_returns_404_for_non_existent_chef()
    {
        $response = $this->getJson("/api/chefs/99999/availability-calendar");

        $response->assertStatus(404);
    }

    /**
     * Helper to get next working day (Sunday-Thursday)
     */
    protected function getNextWorkingDay(): string
    {
        $date = now();
        while (!in_array($date->dayOfWeek, [0, 1, 2, 3, 4])) {
            $date->addDay();
        }
        return $date->format('Y-m-d');
    }
}
