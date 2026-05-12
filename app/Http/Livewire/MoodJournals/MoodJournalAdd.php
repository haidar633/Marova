<?php

namespace App\Http\Livewire\MoodJournals;

use App\Models\MoodJournal;
use App\Models\VibeCheck;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class MoodJournalAdd extends Component
{
    use AuthorizesRequests;

    public $entry_date;
    public $entry_time;
    public $emotional_state = 'Secure';
    public $pov;
    public $discussed_with_partner = false;
    public $vibe_check_id;

    protected $rules = [
        'entry_date' => 'required|date',
        'entry_time' => 'nullable|date_format:H:i',
        'emotional_state' => 'required|in:Secure,Anxious/Toxic,Overwhelmed,Happy,Sad,Angry,Calm,Excited,Stressed,Other',
        'pov' => 'nullable|string|max:2000',
        'discussed_with_partner' => 'boolean',
        'vibe_check_id' => 'nullable|exists:vibe_checks,id',
    ];

    public function mount()
    {
        $this->entry_date = now()->format('Y-m-d');
        $this->entry_time = now()->format('H:i');
    }

    public function store()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            MoodJournal::create([
                'user_id' => Auth::id(),
                'entry_date' => $this->entry_date,
                'entry_time' => $this->entry_time,
                'emotional_state' => $this->emotional_state,
                'pov' => $this->pov,
                'discussed_with_partner' => $this->discussed_with_partner,
                'vibe_check_id' => $this->vibe_check_id,
            ]);

            DB::commit();

            session()->flash('success', 'Mood journal entry created successfully');
            return redirect()->route('mood-journals');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error creating mood journal entry: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $activeVibeCheck = VibeCheck::where('user_id', Auth::id())
            ->where('is_active', true)
            ->first();

        return view('livewire.mood-journals.mood-journal-add', compact('activeVibeCheck'));
    }
}
