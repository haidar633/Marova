<?php

namespace App\Http\Livewire\Attempts;

use App\Models\Attempt;
use App\Models\Position;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttemptView extends Component
{
    use WithPagination;

    public $attempts;
    public $editingRating = null;
    public $tempRating = null;
    public $hoverRating = null;

    public $dateFrom;
    public $dateTo;
    public $attemptType = '';
    public $successfulFilter = '';

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'tempRating' => 'nullable|numeric|min:0.5|max:5',
    ];

    public $positionsMap = [];

    public function mount()
    {
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->positionsMap = Position::all()->keyBy('id');
    }

    #[On('destroy')]
    public function destroy($id)
    {
        Attempt::where('id', $id)->delete();

        return redirect()->route('attempts')
            ->with('success', 'Attempt deleted successfully');
    }

    public function getAttemptsProperty()
    {
        $query = Attempt::with(['creator', 'updater'])
            ->where('created_by', Auth::id())
            ->orderBy('attempt_date', 'desc')
            ->orderBy('attempt_time', 'desc');

        if ($this->dateFrom) {
            $query->where('attempt_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('attempt_date', '<=', $this->dateTo);
        }

        if ($this->attemptType) {
            $query->where('attempt_type', $this->attemptType);
        }

        if ($this->successfulFilter !== '') {
            $query->where('successful', $this->successfulFilter);
        }

        return $query->paginate(10);
    }

    public function getGroupedAttemptsProperty()
    {
        $attempts = $this->getAttemptsProperty();
        $grouped = [];

        foreach ($attempts as $attempt) {
            $date = Carbon::parse($attempt->attempt_date)->format('Y-m-d');
            $grouped[$date][] = $attempt;
        }

        return $grouped;
    }


    public function clearFilters()
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->attemptType = '';
        $this->successfulFilter = '';
        $this->resetPage();
    }

    public function updatingDateFrom() { $this->resetPage(); }
    public function updatingDateTo() { $this->resetPage(); }
    public function updatingAttemptType() { $this->resetPage(); }
    public function updatingSuccessfulFilter() { $this->resetPage(); }

    public function render()
    {
        return view('livewire.attempts.attempt-view', [
            'groupedAttempts' => $this->groupedAttempts,
            'positionsMap' => $this->positionsMap,
        ]);
    }
}
