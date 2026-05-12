<?php

namespace App\Http\Livewire\TalkTrackers;

use App\Models\TalkTracker;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TalkTrackerShow extends Component
{
    use AuthorizesRequests;

    public $talkTracker;

    public function mount($id)
    {
        $this->talkTracker = TalkTracker::where('user_id', Auth::id())
            ->with(['moodJournal'])
            ->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.talk-trackers.talk-tracker-show');
    }
}
