<?php

namespace App\Http\Livewire\Bills;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;

class BillEdit extends Component
{
    use AuthorizesRequests;

    public $id;
    public $user_id = '';
    public $description = '';
    public $paid = '';
    public $patients;
    public $isSubmitting = false;
    public $bill;

    protected function rules()
    {
        return [
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    return $query->where('type', 'Patient');
                }),
            ],
            'description' => ['required', 'string', 'max:255'],
            'paid' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    protected $messages = [
        'user_id.exists' => 'Please select a valid patient.',
        'paid.min' => 'Amount must be greater than zero.',
    ];

    public function mount($id)
    {
        $this->authorize('bill-edit');

//        $this->bill = $id;
        $this->user_id = $id->user_id;
        $this->description = $id->description;
        $this->paid = $id->paid;

        $this->loadPatients();
        $this->dispatch('billUpdated');
    }

    protected function loadPatients()
    {
        $this->patients = User::where('type', 'Patient')
            ->select('id', 'fname', 'lname')
            ->orderBy('fname')
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => "{$patient->fname} {$patient->lname}"
                ];
            });
    }

    public function updatedUserId()
    {
        $this->validateOnly('user_id');
    }

    public function updatedDescription()
    {
        $this->validateOnly('description');
    }

    public function updatedAmount()
    {
        $this->validateOnly('paid');
    }

    public function update()
    {
        if ($this->isSubmitting) {
            return;
        }

        $this->isSubmitting = true;
        $this->dispatch('scrollToElement');

        try {
            $validatedData = $this->validate();

            // Verify that the user is actually a patient
            $patient = User::where('id', $this->user_id)
                ->where('type', 'Patient')
                ->firstOrFail();

            $this->bill->update([
                'user_id' => $patient->id,
                'description' => trim($this->description),
                'paid' => $this->paid,
            ]);

            session()->flash('success', 'Bill updated successfully.');
            return redirect()->route('bills');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Selected patient not found or is invalid.');
        } catch (\Exception $e) {
            report($e);
            session()->flash('error', 'Error updating bill. Please try again.');
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function render()
    {
        return view('livewire.Bills.bill-edit');
    }
}
