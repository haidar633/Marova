<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;


class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [

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

            'report-list',
            'report-create',
            'report-edit',
            'report-delete',

            'favposition-list',

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
            'favorate-hub-delete',
            'favorate-hub-edit',

            'activity-tracker'

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
