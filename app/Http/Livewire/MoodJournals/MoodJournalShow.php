<?php

namespace App\Http\Livewire\MoodJournals;

use App\Models\MoodJournal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MoodJournalShow extends Component
{
    use AuthorizesRequests;

    public $moodJournal;

    public function mount($id)
    {
        $this->moodJournal = MoodJournal::where('user_id', Auth::id())
            ->with(['vibeCheck', 'talkTrackers'])
            ->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.mood-journals.mood-journal-show');
    }
}
