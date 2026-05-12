<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AddPartnerPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:add-partners';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add partner permissions to existing roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $partnerPermissions = [
            'partner-list',
            'partner-create',
            'partner-edit',
            'partner-delete',
        ];

        // Ensure permissions exist
        foreach ($partnerPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->info("Permission '{$permission}' ensured.");
        }

        // Get all roles
        $roles = Role::all();

        foreach ($roles as $role) {
            // Add partner permissions to Admin and SuperAdmin roles
            if (in_array($role->name, ['Admin', 'SuperAdmin'])) {
                foreach ($partnerPermissions as $permission) {
                    if (!$role->hasPermissionTo($permission)) {
                        $role->givePermissionTo($permission);
                        $this->info("Added '{$permission}' to role '{$role->name}'");
                    } else {
                        $this->line("Role '{$role->name}' already has '{$permission}'");
                    }
                }
            }
        }

        $this->info('Partner permissions have been added successfully!');
        return 0;
    }
}
