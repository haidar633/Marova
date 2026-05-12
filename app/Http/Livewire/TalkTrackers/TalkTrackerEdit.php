<?php

namespace App\Http\Livewire\TalkTrackers;

use App\Models\TalkTracker;
use App\Models\MoodJournal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class TalkTrackerEdit extends Component
{
    use AuthorizesRequests;

    public $talkTracker;
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

    public function mount($id)
    {
        $this->talkTracker = TalkTracker::where('user_id', Auth::id())->findOrFail($id);
        $this->conversation_date = $this->talkTracker->conversation_date->format('Y-m-d');
        // conversation_time is a TIME field (string), extract H:i format for HTML time input
        $this->conversation_time = $this->talkTracker->conversation_time ? substr($this->talkTracker->conversation_time, 0, 5) : null;
        $this->topic = $this->talkTracker->topic;
        $this->connection_rating = $this->talkTracker->connection_rating;
        $this->resolution_summary = $this->talkTracker->resolution_summary;
        $this->mood_journal_id = $this->talkTracker->mood_journal_id;
    }

    public function update()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $this->talkTracker->update([
                'conversation_date' => $this->conversation_date,
                'conversation_time' => $this->conversation_time,
                'topic' => $this->topic,
                'connection_rating' => $this->connection_rating,
                'resolution_summary' => $this->resolution_summary,
                'mood_journal_id' => $this->mood_journal_id,
            ]);

            DB::commit();

            session()->flash('success', 'Talk tracker entry updated successfully');
            return redirect()->route('talk-trackers');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error updating talk tracker entry: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $moodJournals = MoodJournal::where('user_id', Auth::id())
            ->orderBy('entry_date', 'desc')
            ->get();

        return view('livewire.talk-trackers.talk-tracker-edit', compact('moodJournals'));
    }
}
