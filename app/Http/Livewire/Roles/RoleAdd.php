<?php

namespace App\Http\Livewire\Roles;

use Livewire\Component;
use DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class RoleAdd extends Component
{
    public $name;
    public $permission;
    public $role;

    public $searchTerm = ''; // New property for search term

    public $selectAll = false;
    public $notBooted = true;
    public $groupSelectAll = []; // Group select all tracking

    public $selectedPermission = [];
    public $filteredPermissions = [];
    public $allpermissions = [];

    protected $rules = [
        'name' => 'required|unique:roles,name',
        'permission' => 'required'
    ];

    public function mount()
    {
        $this->authorize('role-create');

        $this->loadPermissions();
    }

    // Method to load permissions with optional search functionality
    public function loadPermissions()
    {
        $this->permission = Permission::query()
            ->when($this->searchTerm, function ($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->get();


//        $newPermissions = [
//            Permission::firstOrCreate(['name' => 'view-broker-commission']),
//            Permission::firstOrCreate(['name' => 'view-net-premium'])
//        ];


        $tempobj = [];


        foreach ($this->permission as $key => $p) {
            $permissionName = explode("-", $p->name);

            // Ensure there is at least one part after explode
            $permHead = $permissionName[0];
            $permSubName = count($permissionName) == 2 ? $permissionName[1] ?? '' : $permissionName[1] . '-' . $permissionName[2] ?? ''; // Handle missing sub-name case

            // Initialize array key if not exists
            if (!isset($tempobj[$permHead])) {
                $tempobj[$permHead] = [];
            }

            // Assign extracted sub-name
            $p->permissionSubName = $permSubName;

            // Append the permission object to the respective group
            $tempobj[$permHead][] = $p;
        }

        $this->filteredPermissions = $tempobj;

        $this->allpermissions = DB::table('role_has_permissions')
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
    }

    // Trigger search when the search term is updated
    public function updatedSearchTerm()
    {
        $this->loadPermissions();
    }

    public function selectAllPermissions()
    {
        if ($this->selectAll) {
            foreach ($this->filteredPermissions as $groupName => $permissions) {
                $this->groupSelectAll[$groupName] = true;
                foreach ($permissions as $permission) {
                    $this->selectedPermission[$permission->id] = true;
                }
            }
        } else {
            $this->selectedPermission = [];
            foreach ($this->filteredPermissions as $groupName => $permissions) {
                $this->groupSelectAll[$groupName] = false;
            }
        }
    }

    public function selectGroupPermissions($groupName)
    {
        $isChecked = $this->groupSelectAll[$groupName] ?? false;

        foreach ($this->filteredPermissions[$groupName] as $permission) {
            if ($isChecked) {
                $this->selectedPermission[$permission->id] = true;
            } else {
                unset($this->selectedPermission[$permission->id]);
            }
        }

        // After group selection, check if ALL permissions are selected globally
        $allPermissionIds = collect($this->permission)->pluck('id')->toArray();
        $selectedIds = array_keys(array_filter($this->selectedPermission));

        $this->selectAll = count(array_diff($allPermissionIds, $selectedIds)) === 0;
    }


    public function store()
    {
        $this->dispatch('scrollToElement');

        $validatedData = $this->validate([
            'name' => 'required|unique:roles,name',
            'selectedPermission' => 'required'
        ]);

        $this->dispatch('saved');

        foreach ($validatedData['selectedPermission'] as $key => $value) {
            if ($value == false) {
                unset($validatedData['selectedPermission'][$key]);
            }
        }



        $role = Role::create(['name' => $validatedData['name']]);
        $role->syncPermissions(array_keys($validatedData['selectedPermission']));

        return redirect()->route('roles')
            ->with('success', 'Role created successfully.');
    }

    public function render()
    {
        $this->dispatch('cardLoaded', true);

        return view('livewire.Roles.role-add');
    }
}
