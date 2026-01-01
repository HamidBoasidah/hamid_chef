<?php

namespace Tests\Feature;

use App\Models\Chef;
use App\Models\ChefVacation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Carbon\Carbon;

class ChefVacationMonthlyApiTest extends TestCase
{
    use RefreshDatabase;

    protected Chef $chef;

    protected function setUp(): void
    {
        parent::setUp();
        $this->chef = Chef::factory()->create();
        Sanctum::actingAs($this->chef->user);
    }

    /** @test */
    public function lists_vacations_for_specified_month_only()
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $nextMonth = Carbon::now()->addMonth()->startOfMonth();

        // Create vacations across two months
        ChefVacation::factory()->create(['chef_id' => $this->chef->id, 'date' => $thisMonth->copy()->addDays(5)->format('Y-m-d')]);
        ChefVacation::factory()->create(['chef_id' => $this->chef->id, 'date' => $thisMonth->copy()->addDays(15)->format('Y-m-d')]);
        ChefVacation::factory()->create(['chef_id' => $this->chef->id, 'date' => $nextMonth->copy()->addDays(3)->format('Y-m-d')]);

        $monthStr = $thisMonth->format('Y-m');
        $response = $this->getJson('/api/chef/vacations/monthly?month=' . $monthStr);

        $response->assertOk()
                 ->assertJson(['success' => true])
                 ->assertJsonCount(2, 'data');
        $dates = array_map(fn($i) => $i['date'], $response->json('data'));
        $this->assertContains($thisMonth->copy()->addDays(5)->format('Y-m-d'), $dates);
        $this->assertContains($thisMonth->copy()->addDays(15)->format('Y-m-d'), $dates);
        $this->assertNotContains($nextMonth->copy()->addDays(3)->format('Y-m-d'), $dates);
    }

    /** @test */
    public function defaults_to_current_month_when_month_not_provided()
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $prevMonth = Carbon::now()->subMonth()->startOfMonth();

        ChefVacation::factory()->create(['chef_id' => $this->chef->id, 'date' => $thisMonth->copy()->addDays(7)->format('Y-m-d')]);
        ChefVacation::factory()->create(['chef_id' => $this->chef->id, 'date' => $prevMonth->copy()->addDays(10)->format('Y-m-d')]);

        $response = $this->getJson('/api/chef/vacations/monthly');

        $response->assertOk()
                 ->assertJson(['success' => true]);
        $dates = array_map(fn($i) => $i['date'], $response->json('data'));
        $this->assertContains($thisMonth->copy()->addDays(7)->format('Y-m-d'), $dates);
        // Ensure all returned dates belong to the current month
        $currentMonthStr = $thisMonth->format('Y-m');
        foreach ($dates as $d) {
            $this->assertStringStartsWith($currentMonthStr, $d);
        }
    }
}
