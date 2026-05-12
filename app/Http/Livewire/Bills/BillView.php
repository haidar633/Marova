<?php

namespace App\Http\Livewire\Bills;

use App\Models\Bills;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class BillView extends Component
{
    use AuthorizesRequests, WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $totalRemaining = 0;

    public $search = '';

    protected $listeners = [
        'billDeleted' => '$refresh'
    ];

    // Reset pagination when search changes
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->authorize('bill-list');
    }

    #[On('destroy')]
    public function destroy($id)
    {
        Bills::where('id', $id)->delete();

        $this->dispatch('billDeleted');

        return redirect()->route('bills')
            ->with('success', 'Bill deleted successfully');
    }

    public function render()
    {
        $query = Bills::with(['patient', 'doctor'])->whereHas('patient');

        if (!empty($this->search)) {
            $query->whereHas('patient', function($q) {
                $q->where('fname', 'like', '%' . $this->search . '%')
                    ->orWhere('lname', 'like', '%' . $this->search . '%')
                    ->orWhereRaw("CONCAT(fname, ' ', lname) LIKE ?", ['%' . $this->search . '%']);
            })
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orWhere('total', 'like', '%' . $this->search . '%');
        }

        $bills = $query
            ->where(function($q) {
                $q->where('total', '>', 0)
                    ->orWhere('paid', '>', 0);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $groupedBills = $bills->groupBy(fn($bill) => $bill->patient->fname . ' ' . $bill->patient->lname);

        // Grouped total remaining
        $groupedRemaining = [];
        foreach ($groupedBills as $patientName => $group) {
            $groupedRemaining[$patientName] = $group->sum('total') - $group->sum('paid');
        }

        return view('livewire.Bills.bill-view', [
            'groupedBills' => $groupedBills,
            'groupedRemaining' => $groupedRemaining,
        ]);
    }




}
