<?php

namespace Tests\Unit\Rules;

use App\Models\Chef;
use App\Models\User;
use App\Rules\UniqueChefForUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UniqueChefForUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * **Feature: chef-duplicate-validation, Property 4: Proactive validation prevents database violations**
     * 
     * Test that the UniqueChefForUser rule correctly validates uniqueness
     * for any user attempting to create a chef profile.
     */
    public function test_unique_chef_validation_prevents_duplicates()
    {
        // Property: For any chef creation attempt, the system should verify user_id uniqueness
        for ($i = 0; $i < 10; $i++) {
            // Create a random user
            $user = User::factory()->create();
            
            // Create a chef for this user
            Chef::factory()->create(['user_id' => $user->id]);
            
            // Test the validation rule
            $rule = new UniqueChefForUser($user->id);
            $failed = false;
            
            $rule->validate('user_id', $user->id, function($message) use (&$failed) {
                $failed = true;
                $this->assertEquals('المستخدم لديه بالفعل ملف طاهي', $message);
            });
            
            // Should fail because user already has a chef
            $this->assertTrue($failed, "Validation should fail for user with existing chef profile");
        }
    }

    public function test_validation_passes_for_user_without_chef()
    {
        // Property: For any user without a chef profile, validation should pass
        for ($i = 0; $i < 10; $i++) {
            // Create a random user without a chef
            $user = User::factory()->create();
            
            // Test the validation rule
            $rule = new UniqueChefForUser($user->id);
            $failed = false;
            
            $rule->validate('user_id', $user->id, function($message) use (&$failed) {
                $failed = true;
            });
            
            // Should pass because user doesn't have a chef
            $this->assertFalse($failed, "Validation should pass for user without chef profile");
        }
    }

    public function test_validation_respects_soft_deletes()
    {
        // Create user and chef, then soft delete the chef
        $user = User::factory()->create();
        $chef = Chef::factory()->create(['user_id' => $user->id]);
        $chef->delete(); // Soft delete
        
        // Test the validation rule
        $rule = new UniqueChefForUser($user->id);
        $failed = false;
        
        $rule->validate('user_id', $user->id, function($message) use (&$failed) {
            $failed = true;
        });
        
        // Should pass because chef is soft deleted and our rule respects soft deletes
        $this->assertFalse($failed, "Validation should pass for user with soft-deleted chef");
    }

    public function test_validation_uses_authenticated_user_when_no_user_id_provided()
    {
        $user = User::factory()->create();
        Chef::factory()->create(['user_id' => $user->id]);
        
        // Authenticate the user
        Auth::login($user);
        
        // Test the validation rule without explicit user ID
        $rule = new UniqueChefForUser();
        $failed = false;
        
        $rule->validate('user_id', $user->id, function($message) use (&$failed) {
            $failed = true;
        });
        
        // Should fail because authenticated user has a chef
        $this->assertTrue($failed, "Validation should use authenticated user when no user ID provided");
    }

    public function test_validation_skips_when_no_user_authenticated()
    {
        // Ensure no user is authenticated
        Auth::logout();
        
        // Test the validation rule without user ID
        $rule = new UniqueChefForUser();
        $failed = false;
        
        $rule->validate('user_id', 999, function($message) use (&$failed) {
            $failed = true;
        });
        
        // Should pass because no user is authenticated
        $this->assertFalse($failed, "Validation should skip when no user is authenticated");
    }
}