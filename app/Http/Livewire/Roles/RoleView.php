<?php

namespace App\Http\Livewire\Roles;

use DB;
use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Traits\RoleBasedQueryTrait;

class RoleView extends Component
{
    use AuthorizesRequests, WithPagination,RoleBasedQueryTrait ;

    protected $paginationTheme = 'bootstrap';
    public $search = '';
    public $searchableFields = ['name'];

    protected $role;

    protected $listeners = [
        'destroy',
    ];

    public function mount()
    {
        $this->authorize('role-list');

        $this->role = Role::all();
    }

    #[On('destroy')]
    public function destroy($id)
    {
        Role::where('id', $id)->delete();

        return redirect()->route('roles')
            ->with('success', 'Role deleted successfully');
    }

    public function render()
    {
        $query = $this->getScopedAndSearchedData(Role::class);

        $query = $query->orderBy('id', 'ASC')->whereNotIN('id', [1]);

        $this->role = $query->searchMany($this->searchableFields, $this->search);

        return view('livewire.Roles.role-view', [
            'role' => $this->role,
        ]);
    }
}
