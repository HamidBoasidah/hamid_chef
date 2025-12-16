<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreChefRequest;
use App\Models\Chef;
use App\Models\User;
use App\Models\Governorate;
use App\Models\District;
use App\Models\Area;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreChefRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create required location data for tests
        $governorate = Governorate::factory()->create();
        $district = District::factory()->create(['governorate_id' => $governorate->id]);
        Area::factory()->create(['district_id' => $district->id]);
    }

    /**
     * **Feature: chef-duplicate-validation, Property 1: Duplicate chef creation returns structured validation response**
     * 
     * Test that StoreChefRequest properly validates duplicate chef creation attempts
     * and returns structured validation responses.
     */
    public function test_duplicate_chef_validation_returns_structured_response()
    {
        // Property: For any user with an existing chef profile, validation should fail with proper structure
        for ($i = 0; $i < 5; $i++) {
            // Create a random user with a chef profile
            $user = User::factory()->create();
            Chef::factory()->create(['user_id' => $user->id]);
            
            // Authenticate the user
            Auth::login($user);
            
            // Create valid request data
            $requestData = [
                'user_id' => $user->id, // Include user_id to trigger validation
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'address' => fake()->address(),
                'base_hourly_rate' => fake()->randomFloat(2, 10, 100),
                'is_active' => fake()->boolean(),
                'governorate_id' => Governorate::first()->id,
                'district_id' => District::first()->id,
                'area_id' => Area::first()->id,
            ];
            
            // Create and validate the request
            $request = new StoreChefRequest();
            $validator = Validator::make($requestData, $request->rules());
            
            // Should fail validation
            $this->assertTrue($validator->fails(), "Validation should fail for user with existing chef");
            
            // Check that user_id field has the expected error
            $errors = $validator->errors();
            $this->assertTrue($errors->has('user_id'), "Should have user_id validation error");
            $this->assertStringContainsString('المستخدم لديه بالفعل ملف طاهي', $errors->first('user_id'));
        }
    }

    public function test_validation_passes_for_user_without_chef()
    {
        // Property: For any user without a chef profile, validation should pass
        for ($i = 0; $i < 5; $i++) {
            // Create a random user without a chef profile
            $user = User::factory()->create();
            
            // Authenticate the user
            Auth::login($user);
            
            // Create valid request data
            $requestData = [
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'address' => fake()->address(),
                'base_hourly_rate' => fake()->randomFloat(2, 10, 100),
                'is_active' => fake()->boolean(),
                'governorate_id' => Governorate::first()->id,
                'district_id' => District::first()->id,
                'area_id' => Area::first()->id,
            ];
            
            // Create and validate the request
            $request = new StoreChefRequest();
            $validator = Validator::make($requestData, $request->rules());
            
            // Should pass validation
            $this->assertFalse($validator->fails(), "Validation should pass for user without chef");
        }
    }

    public function test_validation_maintains_existing_rules()
    {
        // Test that existing validation rules still work
        $user = User::factory()->create();
        Auth::login($user);
        
        // Test with missing required fields
        $requestData = [
            // Missing required name, governorate_id, district_id, area_id
            'email' => 'invalid-email', // Invalid email format
            'base_hourly_rate' => -10, // Invalid negative rate
        ];
        
        $request = new StoreChefRequest();
        $validator = Validator::make($requestData, $request->rules());
        
        $this->assertTrue($validator->fails());
        $errors = $validator->errors();
        
        // Check that existing validation rules are still enforced
        $this->assertTrue($errors->has('name'));
        $this->assertTrue($errors->has('governorate_id'));
        $this->assertTrue($errors->has('district_id'));
        $this->assertTrue($errors->has('area_id'));
        $this->assertTrue($errors->has('email'));
        $this->assertTrue($errors->has('base_hourly_rate'));
    }

    public function test_validation_handles_unauthenticated_requests()
    {
        // Ensure no user is authenticated
        Auth::logout();
        
        // Create valid request data
        $requestData = [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'governorate_id' => Governorate::first()->id,
            'district_id' => District::first()->id,
            'area_id' => Area::first()->id,
        ];
        
        // Create and validate the request
        $request = new StoreChefRequest();
        $validator = Validator::make($requestData, $request->rules());
        
        // Should pass validation when no user is authenticated
        $this->assertFalse($validator->fails(), "Validation should pass when no user is authenticated");
    }
}