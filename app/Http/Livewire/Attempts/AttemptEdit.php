<?php

namespace App\Http\Livewire\Attempts;

use App\Models\Attempt;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class AttemptEdit extends Component
{
    public $attemptId;
    public $attempt;

    public $successful;
    public $satisfaction_rating;
    public $hover_rating;

    public $description;
    public $attempt_reason;
    public $attempt_time;
    public $attempt_date;
    public $duration_minutes;
    public $lubrication_used;
    public $attempt_type;
    public $selectedPositions = [];

    public $positionOptions = [];
    public $attempt_nb;

    protected $rules = [
        'attempt_date' => 'required|date',
        'successful' => 'boolean',
        'satisfaction_rating' => 'nullable|numeric|min:0.5|max:5|regex:/^\d+(\.[05])?$/',
        'description' => 'nullable|string|max:1000',
        'attempt_reason' => 'nullable|string|max:1000',
        'duration_minutes' => 'nullable|integer|min:0|max:999',
        'lubrication_used' => 'boolean',
        'attempt_type' => 'nullable|string|max:50',
    ];

    public function mount($id)
    {
        $this->attempt = Attempt::findOrFail($id);
        $this->attemptId = $id;

        // Set form values
        $this->successful = $this->attempt->successful;
        $this->satisfaction_rating = $this->attempt->satisfaction_rating;
        $this->attempt_date = $this->attempt->attempt_date;
        $this->attempt_time = $this->attempt->attempt_time;
        $this->description = $this->attempt->description;
        $this->attempt_reason = $this->attempt->attempt_reason;
        $this->duration_minutes = $this->attempt->duration_minutes;
        $this->lubrication_used = $this->attempt->lubrication_used;
        $this->attempt_type = $this->attempt->attempt_type;
        $this->selectedPositions = json_decode($this->attempt->positions ?? '[]');

        $this->positionOptions = Position::orderBy('name')->get(['id', 'name', 'photo'])->toArray();


    }

    #[On('positionSelectize')]
    public function positionSelectize($values)
    {
        $this->selectedPositions = $values;
    }

    public function update()
    {
        $this->validate();

        if ($this->attempt_date !== $this->attempt->attempt_date) {
            $this->attempt_nb = Attempt::whereDate('attempt_date', $this->attempt_date)->count() + 1;
        } else {
            $this->attempt_nb = $this->attempt->attempt_nb; // keep existing
        }


        $this->attempt->update([
            'successful' => $this->successful,
            'satisfaction_rating' => $this->satisfaction_rating,
            'description' => $this->description,
            'attempt_reason' => $this->attempt_reason,
            'attempt_time' => $this->attempt_time,
            'attempt_date' => $this->attempt_date,
            'duration_minutes' => $this->duration_minutes,
            'lubrication_used' => $this->lubrication_used,
            'attempt_type' => $this->attempt_type,
            'positions' => json_encode($this->selectedPositions),
            'attempt_nb' => $this->attempt_nb,
            'updated_by' => Auth::id(),
        ]);

        session()->flash('message', 'Attempt updated successfully!');
        return redirect()->route('attempts');
    }

    public function render()
    {
        return view('livewire.attempts.attempt-edit');
    }
}
