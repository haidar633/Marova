<?php

namespace App\Http\Livewire\Staffs;

use App\Models\User;
use DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class StaffView extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    protected $listeners = [
        'destroy',
    ];

    public function mount()
    {
        $this->authorize('staff-create');

        $this->loadStaffs();
    }


    #[On('destroy')]
    public function destroy($id)
    {
        User::where('id', $id)->delete();

        return redirect()->route('staffs')
            ->with('success', 'Staff deleted successfully');
    }

    protected function loadStaffs()
    {
        $this->staffs = User::where('type', 'Staff')
            ->select('id', 'fname', 'lname','email','created_at')
            ->orderBy('fname')
            ->get();

        if ($this->staffs->isEmpty()) {
            $this->staffs = collect();
        }
    }


    public function store()
    {
        $this->dispatch('scrollToElement');

        $validatedData = $this->validate([
            'selectedPermission' => 'required'
        ]);

        $this->dispatch('saved');



        return redirect()->route('staffs')
            ->with('success', 'Staff created successfully.');
    }

    public function render()
    {
        $query = User::where('type', 'Staff');

        if (!empty($this->search)) {
            $normalizedSearch = str_replace(' ', '', $this->search);

            $query->where(function($q) use ($normalizedSearch) {
                $q->where('fname', 'like', '%' . $this->search . '%')
                    ->orWhere('lname', 'like', '%' . $this->search . '%')
                    ->orWhere('date_of_birth', 'like', '%' . $this->search . '%')
                    ->orWhere('serial_nb', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhereRaw("CONCAT(fname, ' ', lname) LIKE ?", ['%' . $this->search . '%'])
                    ->orWhereHas('contactDetail', function ($contactQuery) use ($normalizedSearch) {
                        $contactQuery->where('value', 'like', '%' . $this->search . '%')
                            ->orWhere('value', 'like', '%' . $normalizedSearch . '%')
                            ->orWhereRaw("REPLACE(value, ' ', '') LIKE ?", ['%' . $normalizedSearch . '%']);
                    });
            });
        }

        $query->orderBy('created_at', 'desc');

        $staffs = $query->select('id', 'serial_nb', 'fname', 'lname', 'date_of_birth', 'email', 'created_at')
            ->paginate(10);

        return view('livewire.Staffs.staff-view', [
            'staffs' => $staffs
        ]);
    }


}
