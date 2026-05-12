<?php

namespace App\Http\Livewire\Bills;

use App\Models\Bills;
use App\Models\Patient;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class BillAdd extends Component
{
    use AuthorizesRequests;

    public $patient_id;
    public $doctor_id;
    public $description;
    public $paid;
    public $selectedBillId = null;

    public $patients = [];
    public $doctors = [];

    public $isSubmitting = false;
    public $existingTotalWithDoctor = 0;
    public $totalPaid = 0;
    public $totalAmount = 0;
    public $totalRemaining = 0;
    public $otherUnpaidBills = [];
    public $canSubmit = true;
    public $errorMessage = '';

    protected function rules()
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['nullable'],
            'description' => ['nullable', 'string'],
            'paid' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function mount()
    {
        $this->authorize('bill-create');
        $this->loadPatients();
        $this->loadDoctors();
    }

    protected function loadPatients()
    {
        $this->patients = Patient::orderBy('fname')->get(['id', 'fname', 'lname'])
            ->map(fn($p) => ['id' => $p->id, 'name' => "{$p->fname} {$p->lname}"]);
    }

    protected function loadDoctors()
    {
        $this->doctors = User::where('type', 'Doctor')->orderBy('fname')->get(['id', 'fname', 'lname', 'email']);
    }

    public function updated($property)
    {
        $this->validateOnly($property);

        if (
            $property === 'paid' &&
            $this->paid > $this->totalRemaining &&
            $this->totalRemaining > 0 &&
            $this->totalAmount > 0
        ) {
            $this->errorMessage = "Payment amount exceeds remaining balance of $" . number_format($this->totalRemaining, 2);
        } else {
            $this->errorMessage = '';
        }

    }

    public function updatedPatientId() { $this->calculateBillSummaries(); }
    public function updatedDoctorId() { $this->calculateBillSummaries(); }

    public function calculateBillSummaries()
    {
        $this->existingTotalWithDoctor = 0;
        $this->totalPaid = 0;
        $this->totalAmount = 0;
        $this->totalRemaining = 0;
        $this->otherUnpaidBills = [];
        $this->canSubmit = true;
        $this->errorMessage = '';

        if ($this->patient_id) {
            $query = Bills::with('doctor')
                ->where('patient_id', $this->patient_id)
                ->orderBy('created_at', 'desc');

            if ($this->doctor_id) {
                $query->where('user_id', $this->doctor_id);
            }

            $this->otherUnpaidBills = $query->get()->filter(function ($bill) {
                return $bill->total > 0 || $bill->paid > 0 || ($bill->total - $bill->paid) !== 0.0;
            });

            $this->totalAmount = $this->otherUnpaidBills->sum('total');
            $this->totalPaid = $this->otherUnpaidBills->sum('paid');
            $this->totalRemaining = $this->totalAmount - $this->totalPaid;

            if ($this->doctor_id) {
                $this->existingTotalWithDoctor = $this->totalRemaining;
            }

            // Check for disabling submit
            if ($this->otherUnpaidBills->isEmpty()) {
                $this->canSubmit = false;
            }

            if ($this->paid > $this->totalRemaining && $this->totalRemaining > 0 && $this->totalAmount > 0) {
                $this->canSubmit = false;
                $this->errorMessage = "Payment amount exceeds remaining balance of $" . number_format($this->totalRemaining, 2);
            } else {
                $this->errorMessage = '';
            }

            // Adjust paid if it's too high
            if ($this->paid > $this->totalRemaining && $this->totalRemaining > 0) {
                $this->paid = $this->totalRemaining;
            }
        }
    }

    public function fillSpecificBill($billId)
    {
        $bill = Bills::find($billId);
        if ($bill) {
            $remaining = $bill->total - $bill->paid;
            if ($remaining > 0) {
                $this->paid = $remaining;
                $this->selectedBillId = $billId;
                $this->description = $bill->description;
                $this->errorMessage = '';
            } else {
                $this->errorMessage = "This bill is already fully paid.";
            }
        }
    }

    public function store()
    {
        if ($this->isSubmitting) return;

        if ($this->totalRemaining > 0 && $this->paid > $this->totalRemaining) {
            $this->errorMessage = "Payment amount exceeds remaining balance of $" . number_format($this->totalRemaining, 2);
            return;
        }

        $this->isSubmitting = true;
        $this->validate();
        $this->dispatch('scrollToElement');

        try {
            DB::beginTransaction();

            $remainingPayment = $this->paid;

            if ($this->selectedBillId) {
                $bill = Bills::find($this->selectedBillId);
                if ($bill) {
                    $due = $bill->total - $bill->paid;

                    if ($due > 0) {
                        $paymentAmount = min($remainingPayment, $due);

                        // Create separate payment entry (fixed: explicitly set description)
                        Bills::create([
                            'patient_id' => $this->patient_id,
                            'user_id' => $this->doctor_id,
                            'appointment_id' => $bill->appointment_id,
                            'description' => $this->description ?? $bill->description,
                            'paid' => $paymentAmount,
                            'total' => 0,
                        ]);

                        $remainingPayment -= $paymentAmount;
                    }
                }
            } else {
                $unpaidBills = $this->otherUnpaidBills
                    ->filter(fn($bill) => ($bill->total - $bill->paid) > 0)
                    ->sortBy('created_at');

                foreach ($unpaidBills as $bill) {
                    if ($remainingPayment <= 0) break;

                    $due = $bill->total - $bill->paid;
                    $paymentForThisBill = min($remainingPayment, $due);

                    if ($paymentForThisBill > 0) {
                        // Fixed: explicitly set description
                        Bills::create([
                            'patient_id' => $this->patient_id,
                            'user_id' => $bill->user_id,
                            'appointment_id' => $bill->appointment_id,
                            'description' => $this->description ?? $bill->description,
                            'paid' => $paymentForThisBill,
                            'total' => 0,
                        ]);

                        $remainingPayment -= $paymentForThisBill;
                    }
                }

                // Optional: Handle excess payment as a credit or show an error
                if ($remainingPayment > 0) {
                    session()->flash('warning', 'There was an excess amount of $' . number_format($remainingPayment, 2));
                }
            }

            DB::commit();

            $this->dispatch('saved');
            session()->flash('success', 'Payment processed successfully.');
            return redirect()->route('bills');

        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            session()->flash('error', 'Error processing payment: ' . $e->getMessage());
        } finally {
            $this->isSubmitting = false;
            $this->selectedBillId = null;
        }
    }


    public function render()
    {
        return view('livewire.Bills.bill-add');
    }
}
