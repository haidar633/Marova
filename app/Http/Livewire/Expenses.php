<?php

namespace App\Http\Livewire;

use App\Traits\RoleBasedQueryTrait;
use DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Expenses extends Component
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

        $this->role = Role::orderBy('id','ASC')->get();
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
        $this->role = $query->whereNotIN('id', [1])->searchMany($this->searchableFields, $this->search);

        return view('livewire.Roles.role-view', [
            'role' => $this->role,
        ]);
    }
}
