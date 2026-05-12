<?php

namespace App\Http\Livewire\Doctors;

use App\Models\User;
use DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DoctorView extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';


    public $search = '';

    protected $listeners = [
        'destroy',
    ];


    #[On('destroy')]
    public function destroy($id)
    {
        User::where('id', $id)->delete();

        $this->dispatch('doctorDeleted');

        return redirect()->route('doctors')
            ->with('success', 'Doctor deleted successfully');
    }

    public function mount()
    {
        $this->authorize('doctor-create');

        $this->loadDoctors();
    }


    protected function loadDoctors()
    {
        $this->doctors = User::where('type', 'Doctor')
            ->select('id', 'fname', 'lname','email','created_at')
            ->orderBy('fname')
            ->get();

        if ($this->doctors->isEmpty()) {
            $this->doctors = collect();
        }
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $details = $user->userDetail;

        if ($details) {
            $details->dr_is_active = ($details->dr_is_active == '1') ? '0' : '1';
            $details->save();
        } else {
            // If there's no user detail, you can create one with default values if needed
            $user->userDetail()->create([
                'dr_is_active' => '1', // default to active if toggled
            ]);
        }

        $this->dispatch('doctorStatusUpdated'); // optional JS event
    }


    public function store()
    {
        $this->dispatch('scrollToElement');

        $validatedData = $this->validate([
            'selectedPermission' => 'required'
        ]);

        $this->dispatch('saved');



        return redirect()->route('doctors')
            ->with('success', 'Doctor created successfully.');
    }

    public function render()
    {
        $query = User::where('type', 'Doctor');

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
        $doctors = $query->select('id', 'serial_nb', 'fname', 'lname', 'date_of_birth', 'email', 'created_at')
            ->paginate(10);

        return view('livewire.Doctors.doctor-view', [
            'doctors' => $doctors
        ]);
    }


}
