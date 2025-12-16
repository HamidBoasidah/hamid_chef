<?php

namespace Tests\Unit;

use App\Exceptions\ValidationException;
use App\Http\Controllers\Api\ChefController;
use App\Models\Chef;
use App\Models\User;
use App\Models\Governorate;
use App\Models\District;
use App\Models\Area;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ResponseFormatConsistencyTest extends TestCase
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
     * Test that duplicate chef errors follow the established JSON response structure.
     */
    public function test_duplicate_chef_error_follows_established_json_structure()
    {
        // Property: For any duplicate chef error, response should have consistent structure
        // Create ValidationException as it would be created for duplicate chef
        $validationException = ValidationException::withMessages([
            'user_id' => ['المستخدم لديه بالفعل ملف طاهي']
        ]);
        
        // Create a mock request
        $request = Request::create('/api/chefs', 'POST');
        
        // Render the exception
        $response = $validationException->render($request);
        
        // Verify response structure
        $this->assertEquals(422, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        
        // Check all required fields exist
        $this->assertArrayHasKey('success', $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('error_code', $responseData);
        $this->assertArrayHasKey('status_code', $responseData);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertArrayHasKey('timestamp', $responseData);
        
        // Check field values
        $this->assertFalse($responseData['success']);
        $this->assertEquals('بيانات غير صحيحة', $responseData['message']);
        $this->assertEquals('VALIDATION_ERROR', $responseData['error_code']);
        $this->assertEquals(422, $responseData['status_code']);
        
        // Check errors structure
        $this->assertIsArray($responseData['errors']);
        $this->assertArrayHasKey('user_id', $responseData['errors']);
        $this->assertContains('المستخدم لديه بالفعل ملف طاهي', $responseData['errors']['user_id']);
        
        // Check timestamp format (ISO 8601)
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d+Z$/',
            $responseData['timestamp']
        );
    }

    /**
     * **Feature: chef-duplicate-validation, Property 5: Error messages are consistent and localized**
     * 
     * Test that error messages are in Arabic and consistent with system patterns.
     */
    public function test_error_messages_are_consistent_and_localized()
    {
        // Property: For any duplicate chef error, messages should be in Arabic and consistent
        $validationException = ValidationException::withMessages([
            'user_id' => ['المستخدم لديه بالفعل ملف طاهي']
        ]);
        
        $request = Request::create('/api/chefs', 'POST');
        $response = $validationException->render($request);
        $responseData = json_decode($response->getContent(), true);
        
        // Check main message is in Arabic
        $this->assertEquals('بيانات غير صحيحة', $responseData['message']);
        
        // Check field-specific message is in Arabic
        $this->assertContains('المستخدم لديه بالفعل ملف طاهي', $responseData['errors']['user_id']);
        
        // Verify no English text in error messages
        $this->assertStringNotContainsString('user', strtolower($responseData['message']));
        $this->assertStringNotContainsString('chef', strtolower($responseData['errors']['user_id'][0]));
        $this->assertStringNotContainsString('already', strtolower($responseData['errors']['user_id'][0]));
    }

    public function test_response_format_matches_other_validation_errors()
    {
        // Create two different validation exceptions to compare structure
        $duplicateChefException = ValidationException::withMessages([
            'user_id' => ['المستخدم لديه بالفعل ملف طاهي']
        ]);
        
        $generalValidationException = ValidationException::withMessages([
            'name' => ['حقل الاسم مطلوب'],
            'email' => ['تنسيق البريد الإلكتروني غير صحيح']
        ]);
        
        $request = Request::create('/api/test', 'POST');
        
        $duplicateResponse = $duplicateChefException->render($request);
        $generalResponse = $generalValidationException->render($request);
        
        $duplicateData = json_decode($duplicateResponse->getContent(), true);
        $generalData = json_decode($generalResponse->getContent(), true);
        
        // Both should have the same structure
        $this->assertEquals(array_keys($duplicateData), array_keys($generalData));
        
        // Both should have the same status code and error code
        $this->assertEquals($duplicateData['status_code'], $generalData['status_code']);
        $this->assertEquals($duplicateData['error_code'], $generalData['error_code']);
        $this->assertEquals($duplicateData['success'], $generalData['success']);
        $this->assertEquals($duplicateData['message'], $generalData['message']);
        
        // Both should have errors array structure
        $this->assertIsArray($duplicateData['errors']);
        $this->assertIsArray($generalData['errors']);
    }

    public function test_timestamp_format_consistency()
    {
        // Test that timestamps are consistently formatted across multiple responses
        $timestamps = [];
        
        for ($i = 0; $i < 10; $i++) {
            $validationException = ValidationException::withMessages([
                'user_id' => ['المستخدم لديه بالفعل ملف طاهي']
            ]);
            
            $request = Request::create('/api/chefs', 'POST');
            $response = $validationException->render($request);
            $responseData = json_decode($response->getContent(), true);
            
            $timestamps[] = $responseData['timestamp'];
            
            // Each timestamp should be valid ISO 8601 format
            $this->assertMatchesRegularExpression(
                '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d+Z$/',
                $responseData['timestamp']
            );
            
            // Should be parseable as a valid date
            $parsedDate = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $responseData['timestamp']);
            $this->assertNotFalse($parsedDate, "Timestamp should be parseable: " . $responseData['timestamp']);
        }
        
        // All timestamps should be unique (generated at different microseconds)
        $this->assertEquals(count($timestamps), count(array_unique($timestamps)));
    }

    public function test_error_field_structure_consistency()
    {
        // Test that the errors field structure is consistent
        $validationException = ValidationException::withMessages([
            'user_id' => ['المستخدم لديه بالفعل ملف طاهي'],
            'name' => ['حقل الاسم مطلوب'],
            'email' => ['تنسيق البريد الإلكتروني غير صحيح', 'البريد الإلكتروني مستخدم بالفعل']
        ]);
        
        $request = Request::create('/api/chefs', 'POST');
        $response = $validationException->render($request);
        $responseData = json_decode($response->getContent(), true);
        
        // Errors should be an associative array
        $this->assertIsArray($responseData['errors']);
        
        // Each field should have an array of error messages
        foreach ($responseData['errors'] as $field => $messages) {
            $this->assertIsString($field);
            $this->assertIsArray($messages);
            
            // Each message should be a string
            foreach ($messages as $message) {
                $this->assertIsString($message);
                $this->assertNotEmpty($message);
            }
        }
        
        // Specific field checks
        $this->assertArrayHasKey('user_id', $responseData['errors']);
        $this->assertContains('المستخدم لديه بالفعل ملف طاهي', $responseData['errors']['user_id']);
    }
}