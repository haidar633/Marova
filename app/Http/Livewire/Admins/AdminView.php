<?php

namespace App\Http\Livewire\Admins;

use Livewire\Component;
use App\Models\User;
use App\Traits\RoleBasedQueryTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class AdminView extends Component
{
    use AuthorizesRequests, WithPagination, RoleBasedQueryTrait;

    protected $paginationTheme = 'bootstrap';
    public $selectedUser;

    public $search = '';

    protected $listeners = [
        'destroy',
    ];

    public function mount()
    {
        $this->authorize('user-list');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getSelectedUser($id)
    {
        $this->selectedUser = User::find($id);
    }

    #[On('destroy')]
    public function destroy($id)
    {
        User::where('id', $id)->delete();

        return redirect()->route('admins')
            ->with('success', 'User deleted successfully');
    }

    public function render()
    {
        $query = User::where('type', 'Admin');

        if (!empty($this->search)) {
            $normalizedSearch = str_replace(' ', '', $this->search);

            $query->where(function($q) use ($normalizedSearch) {
                $q->where('fname', 'like', '%' . $this->search . '%')
                    ->orWhere('lname', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('date_of_birth', 'like', '%' . $this->search . '%')
                    ->orWhere('serial_nb', 'like', '%' . $this->search . '%')
                    ->orWhereRaw("CONCAT(fname, ' ', lname) LIKE ?", ['%' . $this->search . '%'])
                    ->orWhereHas('contactDetail', function ($contactQuery) use ($normalizedSearch) {
                        $contactQuery->where('value', 'like', '%' . $this->search . '%')
                            ->orWhere('value', 'like', '%' . $normalizedSearch . '%')
                            ->orWhereRaw("REPLACE(value, ' ', '') LIKE ?", ['%' . $normalizedSearch . '%']);
                    });
            });
        }
        $query->orderBy('created_at', 'desc');

        $admins = $query->select('id', 'serial_nb', 'fname', 'lname', 'date_of_birth', 'email', 'created_at')
            ->paginate(10);

        return view('livewire.Admins.admin-view', [
            'admins' => $admins
        ]);
    }
}
