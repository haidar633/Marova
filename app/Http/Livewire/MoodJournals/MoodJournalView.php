<?php

namespace App\Http\Livewire\MoodJournals;

use App\Models\MoodJournal;
use App\Models\VibeCheck;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class MoodJournalView extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $dateFrom;
    public $dateTo;
    public $emotionalStateFilter = '';

    protected $listeners = ['moodJournalDeleted' => '$refresh'];

    public function mount()
    {
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingEmotionalStateFilter()
    {
        $this->resetPage();
    }

    #[On('destroy')]
    public function destroy($id)
    {
        try {
            $moodJournal = MoodJournal::findOrFail($id);
            $moodJournal->delete();

            $this->dispatch('moodJournalDeleted');
            session()->flash('success', 'Mood journal entry deleted successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting mood journal entry: ' . $e->getMessage());
        }
    }

    public function clearFilters()
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->emotionalStateFilter = '';
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = MoodJournal::where('user_id', Auth::id())
            ->with(['vibeCheck']);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('pov', 'like', '%' . $this->search . '%')
                    ->orWhere('emotional_state', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->dateFrom) {
            $query->where('entry_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('entry_date', '<=', $this->dateTo);
        }

        if ($this->emotionalStateFilter) {
            $query->where('emotional_state', $this->emotionalStateFilter);
        }

        $moodJournals = $query->orderBy('entry_date', 'desc')
            ->orderBy('entry_time', 'desc')
            ->paginate(10);

        $activeVibeCheck = VibeCheck::where('user_id', Auth::id())
            ->where('is_active', true)
            ->first();

        return view('livewire.mood-journals.mood-journal-view', compact('moodJournals', 'activeVibeCheck'));
    }
}
