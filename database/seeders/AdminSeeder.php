<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Determine guard and try to pick the "first" role created for that guard
        // (RolesPermissionsSeeder runs before this seeder in DatabaseSeeder)
        $guard = config('acl.guard', 'admin');
        $firstRole = Role::where('guard_name', $guard)->orderBy('id')->first();

        // If no roles exist yet (defensive), ensure there's at least an 'admin' role
        if (! $firstRole) {
            $firstRole = Role::firstOrCreate(
                ['name' => 'admin', 'guard_name' => $guard],
                ['display_name' => ['en' => 'Admin', 'ar' => 'مشرف']]
            );
        }

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
            // Assign the very first role (expected to be Super Admin from RolesPermissionsSeeder)
            $main->assignRole($firstRole->name);
        }

        // Create a few random admins
        Admin::factory(5)->create();
    }
}
