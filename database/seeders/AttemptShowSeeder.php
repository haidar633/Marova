<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class AttemptShowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionName = 'attempt-show';

        // 2. Use firstOrCreate to find the permission or create it if it doesn't exist.
        // This is cleaner and returns the permission object.
        $permission = Permission::firstOrCreate(['name' => $permissionName]);

        // 3. Find the SuperAdmin role.
        $role = Role::where('name', 'SuperAdmin')->first();

        // 4. Check if the role was found before trying to assign the permission.
        if ($role) {
            // 5. Assign the new permission to the SuperAdmin role.
            $role->givePermissionTo($permission);
            $this->command->info("Permission '{$permissionName}' created and assigned to SuperAdmin.");
        } else {
            // If the role doesn't exist, just show a warning. The permission is still created.
            $this->command->warn("Permission '{$permissionName}' was created, but the 'SuperAdmin' role was not found.");
        }
    }
}
