<?php

namespace Tests\Feature\Chef;

use App\Models\Chef;
use App\Models\Kyc;
use App\Models\User;
use App\Models\Governorate;
use App\Models\District;
use App\Models\Area;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChefKycControllerTest extends TestCase
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

        Storage::fake('public');

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
    public function chef_can_view_kyc_index_page()
    {
        $response = $this->actingAs($this->chefUser, 'chef')
            ->get(route('chef.kyc.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Chef/Kyc/Index'));
    }

    /** @test */
    public function chef_can_view_kyc_create_page()
    {
        $response = $this->actingAs($this->chefUser, 'chef')
            ->get(route('chef.kyc.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Chef/Kyc/Create')
            ->has('documentTypes')
        );
    }

    /** @test */
    public function chef_can_create_kyc_request()
    {
        $file = UploadedFile::fake()->create('document.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->chefUser, 'chef')
            ->post(route('chef.kyc.store'), [
                'document_type' => 'id_card',
                'full_name' => 'John Doe',
                'gender' => 'male',
                'date_of_birth' => '1990-01-15',
                'address' => '123 Main Street',
                'document_scan_copy' => $file,
            ]);

        $response->assertRedirect(route('chef.kyc.index'));

        $this->assertDatabaseHas('kycs', [
            'user_id' => $this->chefUser->id,
            'document_type' => 'id_card',
            'full_name' => 'John Doe',
            'gender' => 'male',
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function chef_can_view_own_kyc_request()
    {
        $kyc = Kyc::factory()->create([
            'user_id' => $this->chefUser->id,
            'document_type' => 'passport',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->get(route('chef.kyc.show', $kyc));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Chef/Kyc/Show')
            ->has('kyc')
        );
    }

    /** @test */
    public function chef_cannot_view_other_chef_kyc_request()
    {
        $otherUser = $this->createOtherChef();

        $kyc = Kyc::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->get(route('chef.kyc.show', $kyc));

        $response->assertStatus(403);
    }

    /** @test */
    public function chef_can_edit_pending_kyc_request()
    {
        $kyc = Kyc::factory()->create([
            'user_id' => $this->chefUser->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->get(route('chef.kyc.edit', $kyc));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Chef/Kyc/Edit'));
    }

    /** @test */
    public function chef_can_edit_rejected_kyc_request()
    {
        $kyc = Kyc::factory()->create([
            'user_id' => $this->chefUser->id,
            'status' => 'rejected',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->get(route('chef.kyc.edit', $kyc));

        $response->assertStatus(200);
    }

    /** @test */
    public function chef_cannot_edit_approved_kyc_request()
    {
        $kyc = Kyc::factory()->create([
            'user_id' => $this->chefUser->id,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->get(route('chef.kyc.edit', $kyc));

        $response->assertRedirect(route('chef.kyc.index'));
    }

    /** @test */
    public function chef_can_update_pending_kyc_request()
    {
        $kyc = Kyc::factory()->create([
            'user_id' => $this->chefUser->id,
            'status' => 'pending',
            'document_type' => 'id_card',
            'full_name' => 'Old Name',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->put(route('chef.kyc.update', $kyc), [
                'document_type' => 'passport',
                'full_name' => 'Updated Name',
                'gender' => 'male',
            ]);

        $response->assertRedirect(route('chef.kyc.index'));

        $kyc->refresh();
        $this->assertEquals('passport', $kyc->document_type);
        $this->assertEquals('Updated Name', $kyc->full_name);
        $this->assertEquals('pending', $kyc->status);
    }

    /** @test */
    public function chef_can_delete_pending_kyc_request()
    {
        $kyc = Kyc::factory()->create([
            'user_id' => $this->chefUser->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->delete(route('chef.kyc.destroy', $kyc));

        $response->assertRedirect(route('chef.kyc.index'));
        $this->assertSoftDeleted('kycs', ['id' => $kyc->id]);
    }

    /** @test */
    public function chef_can_delete_rejected_kyc_request()
    {
        $kyc = Kyc::factory()->create([
            'user_id' => $this->chefUser->id,
            'status' => 'rejected',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->delete(route('chef.kyc.destroy', $kyc));

        $response->assertRedirect(route('chef.kyc.index'));
        $this->assertSoftDeleted('kycs', ['id' => $kyc->id]);
    }

    /** @test */
    public function chef_cannot_delete_approved_kyc_request()
    {
        $kyc = Kyc::factory()->create([
            'user_id' => $this->chefUser->id,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->delete(route('chef.kyc.destroy', $kyc));

        $response->assertRedirect(route('chef.kyc.index'));
        $this->assertDatabaseHas('kycs', ['id' => $kyc->id, 'deleted_at' => null]);
    }

    /** @test */
    public function chef_cannot_delete_other_chef_kyc_request()
    {
        $otherUser = $this->createOtherChef();

        $kyc = Kyc::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->delete(route('chef.kyc.destroy', $kyc));

        $response->assertStatus(403);
    }

    /** @test */
    public function kyc_index_shows_only_chef_own_requests()
    {
        // Create KYC for current chef
        Kyc::factory()->create([
            'user_id' => $this->chefUser->id,
        ]);

        // Create another chef with profile
        $otherUser = $this->createOtherChef();

        Kyc::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->chefUser, 'chef')
            ->get(route('chef.kyc.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Chef/Kyc/Index')
            ->has('kycs', 1)
        );
    }

    /** @test */
    public function unauthenticated_user_cannot_access_kyc_pages()
    {
        $response = $this->get(route('chef.kyc.index'));
        $response->assertRedirect(route('chef.login'));
    }

    /** @test */
    public function kyc_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->chefUser, 'chef')
            ->post(route('chef.kyc.store'), []);

        $response->assertSessionHasErrors(['document_type', 'full_name', 'gender', 'document_scan_copy']);
    }

    /** @test */
    public function kyc_creation_validates_document_type()
    {
        $file = UploadedFile::fake()->create('document.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->chefUser, 'chef')
            ->post(route('chef.kyc.store'), [
                'document_type' => 'invalid_type',
                'full_name' => 'John Doe',
                'gender' => 'male',
                'document_scan_copy' => $file,
            ]);

        $response->assertSessionHasErrors(['document_type']);
    }
}
