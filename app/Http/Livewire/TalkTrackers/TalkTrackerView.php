<?php

namespace App\Http\Livewire\TalkTrackers;

use App\Models\TalkTracker;
use App\Models\MoodJournal;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TalkTrackerView extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $dateFrom;
    public $dateTo;
    public $connectionRatingFilter = '';

    protected $listeners = ['talkTrackerDeleted' => '$refresh'];

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

    public function updatingConnectionRatingFilter()
    {
        $this->resetPage();
    }

    #[On('destroy')]
    public function destroy($id)
    {
        try {
            $talkTracker = TalkTracker::where('user_id', Auth::id())->findOrFail($id);
            $talkTracker->delete();

            $this->dispatch('talkTrackerDeleted');
            session()->flash('success', 'Talk tracker entry deleted successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting talk tracker entry: ' . $e->getMessage());
        }
    }

    public function clearFilters()
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->connectionRatingFilter = '';
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = TalkTracker::where('user_id', Auth::id())
            ->with(['moodJournal']);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('topic', 'like', '%' . $this->search . '%')
                    ->orWhere('resolution_summary', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->dateFrom) {
            $query->where('conversation_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('conversation_date', '<=', $this->dateTo);
        }

        if ($this->connectionRatingFilter) {
            $query->where('connection_rating', $this->connectionRatingFilter);
        }

        $talkTrackers = $query->orderBy('conversation_date', 'desc')
            ->orderBy('conversation_time', 'desc')
            ->paginate(10);

        $moodJournals = MoodJournal::where('user_id', Auth::id())
            ->orderBy('entry_date', 'desc')
            ->get();

        return view('livewire.talk-trackers.talk-tracker-view', compact('talkTrackers', 'moodJournals'));
    }
}
