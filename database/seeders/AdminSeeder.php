<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure there's at least one admin role (use app Role model and correct guard/display name)
        $guard = config('acl.guard', 'admin');
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => $guard],
            ['display_name' => ['en' => 'Admin', 'ar' => 'مشرف']]
        );

        // Create a main admin account if not exists
        $mainEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $main = Admin::where('email', $mainEmail)->first();
        if (! $main) {
            // Create directly to avoid factory afterCreating assigning a random role
            $main = Admin::query()->create([
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'email' => $mainEmail,
                'password' => 'password',
                'is_active' => true,
            ]);
            $main->assignRole($adminRole->name);
        }

        // Create a few random admins
        Admin::factory(5)->create();
    }
}
