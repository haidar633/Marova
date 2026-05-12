<?php

namespace App\Http\Livewire\TalkTrackers;

use App\Models\TalkTracker;
use App\Models\MoodJournal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class TalkTrackerAdd extends Component
{
    use AuthorizesRequests;

    public $conversation_date;
    public $conversation_time;
    public $topic;
    public $connection_rating;
    public $resolution_summary;
    public $mood_journal_id;

    protected $rules = [
        'conversation_date' => 'required|date',
        'conversation_time' => 'nullable|date_format:H:i',
        'topic' => 'required|string|max:255',
        'connection_rating' => 'nullable|numeric|min:1|max:5|regex:/^\d+(\.[05])?$/',
        'resolution_summary' => 'nullable|string|max:2000',
        'mood_journal_id' => 'nullable|exists:mood_journals,id',
    ];

    public function mount()
    {
        $this->conversation_date = now()->format('Y-m-d');
        $this->conversation_time = now()->format('H:i');
    }

    public function store()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            TalkTracker::create([
                'user_id' => Auth::id(),
                'conversation_date' => $this->conversation_date,
                'conversation_time' => $this->conversation_time,
                'topic' => $this->topic,
                'connection_rating' => $this->connection_rating,
                'resolution_summary' => $this->resolution_summary,
                'mood_journal_id' => $this->mood_journal_id,
            ]);

            DB::commit();

            session()->flash('success', 'Talk tracker entry created successfully');
            return redirect()->route('talk-trackers');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error creating talk tracker entry: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $moodJournals = MoodJournal::where('user_id', Auth::id())
            ->orderBy('entry_date', 'desc')
            ->get();

        return view('livewire.talk-trackers.talk-tracker-add', compact('moodJournals'));
    }
}
