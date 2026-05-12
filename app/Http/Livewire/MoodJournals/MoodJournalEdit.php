<?php

namespace App\Http\Livewire\MoodJournals;

use App\Models\MoodJournal;
use App\Models\VibeCheck;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class MoodJournalEdit extends Component
{
    use AuthorizesRequests;

    public $moodJournal;
    public $entry_date;
    public $entry_time;
    public $emotional_state;
    public $pov;
    public $discussed_with_partner;
    public $vibe_check_id;

    protected $rules = [
        'entry_date' => 'required|date',
        'entry_time' => 'nullable|date_format:H:i',
        'emotional_state' => 'required|in:Secure,Anxious/Toxic,Overwhelmed,Happy,Sad,Angry,Calm,Excited,Stressed,Other',
        'pov' => 'nullable|string|max:2000',
        'discussed_with_partner' => 'boolean',
        'vibe_check_id' => 'nullable|exists:vibe_checks,id',
    ];

    public function mount($id)
    {
        $this->moodJournal = MoodJournal::where('user_id', Auth::id())->findOrFail($id);
        $this->entry_date = $this->moodJournal->entry_date->format('Y-m-d');
        // entry_time is a TIME field (string), extract H:i format for HTML time input
        $this->entry_time = $this->moodJournal->entry_time ? substr($this->moodJournal->entry_time, 0, 5) : null;
        $this->emotional_state = $this->moodJournal->emotional_state;
        $this->pov = $this->moodJournal->pov;
        $this->discussed_with_partner = $this->moodJournal->discussed_with_partner;
        $this->vibe_check_id = $this->moodJournal->vibe_check_id;
    }

    public function update()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $this->moodJournal->update([
                'entry_date' => $this->entry_date,
                'entry_time' => $this->entry_time,
                'emotional_state' => $this->emotional_state,
                'pov' => $this->pov,
                'discussed_with_partner' => $this->discussed_with_partner,
                'vibe_check_id' => $this->vibe_check_id,
            ]);

            DB::commit();

            session()->flash('success', 'Mood journal entry updated successfully');
            return redirect()->route('mood-journals');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error updating mood journal entry: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $activeVibeCheck = VibeCheck::where('user_id', Auth::id())
            ->where('is_active', true)
            ->first();

        return view('livewire.mood-journals.mood-journal-edit', compact('activeVibeCheck'));
    }
}
