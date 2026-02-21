<?php

namespace Tests\Feature\Chef;

use App\Models\Address;
use App\Models\Chef;
use App\Models\User;
use App\Models\Governorate;
use App\Models\District;
use App\Models\Area;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChefAddressControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $chefUser;
    protected Chef $chef;
    protected Governorate $governorate;
    protected District $district;
    protected Area $area;

    protected function setUp(): void
    {
        parent::setUp();

        // Create location data
        $this->governorate = Governorate::create([
            'name_ar' => 'محافظة اختبار',
            'name_en' => 'Test Governorate',
            'is_active' => true,
        ]);

        $this->district = District::create([
            'name_ar' => 'منطقة اختبار',
            'name_en' => 'Test District',
            'governorate_id' => $this->governorate->id,
            'is_active' => true,
        ]);

        $this->area = Area::create([
            'name_ar' => 'حي اختبار',
            'name_en' => 'Test Area',
            'district_id' => $this->district->id,
            'is_active' => true,
        ]);

        // Create a chef user
        $this->chefUser = User::factory()->create([
            'user_type' => 'chef',
            'is_active' => true,
        ]);

        // Create chef profile
        $this->chef = Chef::create([
            'user_id' => $this->chefUser->id,
            'name' => 'Test Chef',
            'short_description' => 'Test description',
            'email' => $this->chefUser->email,
            'phone' => '123456789',
            'governorate_id' => $this->governorate->id,
            'district_id' => $this->district->id,
            'area_id' => $this->area->id,
            'is_active' => true,
        ]);

        // Refresh the user to load the chef relationship
        $this->chefUser->refresh();
    }

    protected function createOtherChef(): User
    {
        $otherUser = User::factory()->create(['user_type' => 'chef', 'is_active' => true]);
        Chef::create([
            'user_id' => $otherUser->id,
            'name' => 'Other Chef',
            'short_description' => 'Other description',
            'email' => $otherUser->email,
            'phone' => '987654321',
            'governorate_id' => $this->governorate->id,
            'district_id' => $this->district->id,
            'area_id' => $this->area->id,
            'is_active' => true,
        ]);
        return $otherUser;
    }

    /** @test */
    public function chef_can_view_addresses_index_page()
    {
        $response = $this->actingAs($this->chefUser, 'chef')
            ->get(route('chef.addresses.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Chef/Address/Index'));
    }

    /** @test */
    public function chef_can_view_address_create_page()
    {
        $response = $this->actingAs($this->chefUser, 'chef')
            ->get(route('chef.addresses.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Chef/Address/Create')
            ->has('governorates')
            ->has('districts')
            ->has('areas')
        );
    }

    /** @test */
    public function chef_can_create_address()
    {
        $response = $this->actingAs($this->chefUser, 'chef')
            ->post(route('chef.addresses.store'), [
                'label' => 'home',
                'address' => '123 Main Street, Building A',
                'street' => 'Main Street',
                'building_number' => 123,
                'floor_number' => 2,
                'apartment_number' => 5,
                'governorate_id' => $this->governorate->id,
                'district_id' => $this->district->id,
                'area_id' => $this->area->id,
                'lat' => 30.0444,
                'lang' => 31.2357,
                'is_default' => true,
            ]);

        $response->assertRedirect(route('chef.addresses.index'));

        $this->assertDatabaseHas('addresses', [
            'user_id' => $this->chefUser->id,
            'label' => 'home',
            'street' => 'Main Street',
            'building_number' => 123,
            'is_default' => true,
        ]);
    }

    /** @test */
    public function chef_can_edit_own_address()
    {
        $address = Address::create([
            'user_id' => $this->chefUser->id,
            'label' => 'home',
            'address' => 'Old Address',
            'street' => 'Old Street',
            'governorate_id' => $this->governorate->id,
            'district_id' => $this->district->id,
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->get(route('chef.addresses.edit', $address));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Chef/Address/Edit')
            ->has('address')
            ->has('governorates')
        );
    }

    /** @test */
    public function chef_cannot_edit_other_chef_address()
    {
        $otherUser = $this->createOtherChef();
        $address = Address::create([
            'user_id' => $otherUser->id,
            'label' => 'home',
            'address' => 'Other Address',
            'street' => 'Other Street',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->get(route('chef.addresses.edit', $address));

        $response->assertStatus(403);
    }

    /** @test */
    public function chef_can_update_own_address()
    {
        $address = Address::create([
            'user_id' => $this->chefUser->id,
            'label' => 'home',
            'address' => 'Old Address',
            'street' => 'Old Street',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->put(route('chef.addresses.update', $address), [
                'label' => 'work',
                'address' => 'Updated Address',
                'street' => 'Updated Street',
                'building_number' => 50,
            ]);

        $response->assertRedirect(route('chef.addresses.index'));

        $address->refresh();
        $this->assertEquals('work', $address->label);
        $this->assertEquals('Updated Address', $address->address);
        $this->assertEquals('Updated Street', $address->street);
    }

    /** @test */
    public function chef_cannot_update_other_chef_address()
    {
        $otherUser = $this->createOtherChef();
        $address = Address::create([
            'user_id' => $otherUser->id,
            'label' => 'home',
            'address' => 'Other Address',
            'street' => 'Other Street',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->put(route('chef.addresses.update', $address), [
                'label' => 'work',
                'address' => 'Hacked Address',
                'street' => 'Hacked Street',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function chef_can_delete_own_address()
    {
        $address = Address::create([
            'user_id' => $this->chefUser->id,
            'label' => 'home',
            'address' => 'To Delete',
            'street' => 'Delete Street',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->delete(route('chef.addresses.destroy', $address));

        $response->assertRedirect(route('chef.addresses.index'));
        $this->assertSoftDeleted('addresses', ['id' => $address->id]);
    }

    /** @test */
    public function chef_cannot_delete_other_chef_address()
    {
        $otherUser = $this->createOtherChef();
        $address = Address::create([
            'user_id' => $otherUser->id,
            'label' => 'home',
            'address' => 'Other Address',
            'street' => 'Other Street',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->delete(route('chef.addresses.destroy', $address));

        $response->assertStatus(403);
    }

    /** @test */
    public function chef_can_set_address_as_default()
    {
        $address1 = Address::create([
            'user_id' => $this->chefUser->id,
            'label' => 'home',
            'address' => 'Address 1',
            'street' => 'Street 1',
            'is_default' => true,
        ]);

        $address2 = Address::create([
            'user_id' => $this->chefUser->id,
            'label' => 'work',
            'address' => 'Address 2',
            'street' => 'Street 2',
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->patch(route('chef.addresses.default', $address2));

        $response->assertRedirect(route('chef.addresses.index'));

        $address1->refresh();
        $address2->refresh();

        $this->assertFalse((bool) $address1->is_default);
        $this->assertTrue((bool) $address2->is_default);
    }

    /** @test */
    public function chef_cannot_set_other_chef_address_as_default()
    {
        $otherUser = $this->createOtherChef();
        $address = Address::create([
            'user_id' => $otherUser->id,
            'label' => 'home',
            'address' => 'Other Address',
            'street' => 'Other Street',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->patch(route('chef.addresses.default', $address));

        $response->assertStatus(403);
    }

    /** @test */
    public function addresses_index_shows_only_chef_own_addresses()
    {
        // Create address for current chef
        Address::create([
            'user_id' => $this->chefUser->id,
            'label' => 'home',
            'address' => 'My Address',
            'street' => 'My Street',
        ]);

        // Create address for another chef
        $otherUser = $this->createOtherChef();
        Address::create([
            'user_id' => $otherUser->id,
            'label' => 'home',
            'address' => 'Other Address',
            'street' => 'Other Street',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->get(route('chef.addresses.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Chef/Address/Index')
            ->has('addresses', 1)
        );
    }

    /** @test */
    public function unauthenticated_user_cannot_access_address_pages()
    {
        $response = $this->get(route('chef.addresses.index'));
        $response->assertRedirect(route('chef.login'));
    }

    /** @test */
    public function address_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->chefUser, 'chef')
            ->post(route('chef.addresses.store'), []);

        $response->assertSessionHasErrors(['address', 'street']);
    }

    /** @test */
    public function setting_new_default_unsets_previous_default()
    {
        // Create first address as default
        $this->actingAs($this->chefUser, 'chef')
            ->post(route('chef.addresses.store'), [
                'label' => 'home',
                'address' => 'First Address',
                'street' => 'First Street',
                'is_default' => true,
            ]);

        $firstAddress = Address::where('user_id', $this->chefUser->id)->first();
        $this->assertTrue((bool) $firstAddress->is_default);

        // Create second address as default
        $this->actingAs($this->chefUser, 'chef')
            ->post(route('chef.addresses.store'), [
                'label' => 'work',
                'address' => 'Second Address',
                'street' => 'Second Street',
                'is_default' => true,
            ]);

        $firstAddress->refresh();
        $secondAddress = Address::where('user_id', $this->chefUser->id)
            ->where('id', '!=', $firstAddress->id)
            ->first();

        $this->assertFalse((bool) $firstAddress->is_default);
        $this->assertTrue((bool) $secondAddress->is_default);
    }
}
