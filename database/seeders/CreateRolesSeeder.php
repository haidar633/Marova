<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CreateRolesSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::create(['name' => 'Admin']);

        $doctorRole = Role::create(['name' => 'Doctor']);

        $staffRole = Role::create(['name' => 'Staff']);


        // Define permissions
        $permissions = [
            'appointment-list',
        ];

        // all permissions
        $allPermissions = [
            'dashboard-view',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',


            'role-list',
            'role-create',
            'role-edit',
            'role-delete',


            'staff-list',
            'staff-create',
            'staff-edit',
            'staff-delete',

            'admin-list',
            'admin-create',
            'admin-edit',
            'admin-delete',




            'appointment-list',
            'appointment-create',
            'appointment-edit',
            'appointment-delete',

            'position-list',
            'position-create',
            'position-edit',
            'position-delete',

            'partner-list',
            'partner-create',
            'partner-edit',
            'partner-delete',

            'attempt-list',
            'attempt-create',
            'attempt-edit',
            'attempt-delete',

            'favorate-hub-list',
            'favorate-hub-create',
            'favorate-hub-edit',
            'favorate-hub-delete',

            'favposition-list',

            'report-list',
            'report-create',
            'report-edit',
            'report-delete',

            'activity-tracker',


        ];


        // Assign permissions to admin


        foreach ($permissions as $permission) {
            $doctorRole->givePermissionTo($permission);
            $staffRole->givePermissionTo($permission);
        }

        foreach ($allPermissions as $all) {
            $adminRole->givePermissionTo($all);
        }
    }
}
