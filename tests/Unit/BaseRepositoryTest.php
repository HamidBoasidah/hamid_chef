<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Address;
use App\Models\User;
use App\Repositories\AddressRepository;

class BaseRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_method_accepts_model_instance()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id, 'label' => 'Old Label']);
        
        $repository = new AddressRepository(new Address());
        
        // Test new update method that accepts Model instance
        $updated = $repository->update($address, ['label' => 'New Label']);
        
        $this->assertEquals('New Label', $updated->label);
        $this->assertEquals($address->id, $updated->id);
    }

    public function test_update_by_id_method_still_works_for_backward_compatibility()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id, 'label' => 'Old Label']);
        
        $repository = new AddressRepository(new Address());
        
        // Test old updateById method for backward compatibility
        $updated = $repository->updateById($address->id, ['label' => 'New Label']);
        
        $this->assertEquals('New Label', $updated->label);
        $this->assertEquals($address->id, $updated->id);
    }

    public function test_query_method_returns_clean_query_builder()
    {
        $repository = new AddressRepository(new Address());
        
        $query = $repository->query();
        
        // Verify it returns a query builder
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $query);
    }
}
