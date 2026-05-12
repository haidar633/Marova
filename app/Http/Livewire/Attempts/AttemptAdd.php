<?php

namespace App\Http\Livewire\Attempts;

use App\Models\Attempt;
use App\Models\Position;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AttemptAdd extends Component
{
    // Form fields
    public $successful = false;
    public $hover_rating;
    public $description;
    public $attempt_reason;
    public $attempt_time;
    public $attempt_date;
    public $duration_minutes;
    public $lubrication_used = false;
    public $attempt_type;
    public $position_id;

    // Component data
    public $positions = [];

    public $selectedPositions = [];
    public $positionOptions = [];

    public $satisfaction_rating;

    public $attempt_nb; // NEW


    protected $rules = [
        'attempt_date' => 'required|date',
        'attempt_time' => 'nullable|date_format:H:i',
        'successful' => 'boolean',
        'satisfaction_rating' => 'nullable|numeric|min:0.5|max:5|regex:/^\d+(\.[05])?$/',
        'description' => 'nullable|string|max:1000',
        'attempt_reason' => 'nullable|string|max:1000',
        'duration_minutes' => 'nullable|integer|min:0|max:999',
        'lubrication_used' => 'boolean',
        'attempt_type' => 'nullable|string|max:50',
        'position_id' => 'nullable|exists:positions,id',
    ];

    protected $messages = [
        'attempt_date.required' => 'The attempt date is required.',
        'attempt_date.date' => 'Please enter a valid date.',
        'satisfaction_rating.numeric' => 'Satisfaction rating must be a number.',
        'satisfaction_rating.min' => 'Satisfaction rating must be at least 0.5.',
        'satisfaction_rating.max' => 'Satisfaction rating cannot exceed 5.',
        'satisfaction_rating.regex' => 'Satisfaction rating must be in increments of 0.5 (e.g., 1.0, 1.5, 2.0).',
        'duration_minutes.integer' => 'Duration must be a number.',
        'duration_minutes.min' => 'Duration cannot be negative.',
        'position_id.exists' => 'The selected position is invalid.',
        'selectedPositions' => 'nullable|array',
        'selectedMPositions.*' => 'exists:materials,id',
    ];

    public function mount()
    {
        // Set default date to today
        $this->attempt_date = now()->format('Y-m-d');
        $this->attempt_time = now()->format('H:i');

        $this->positionOptions = Position::orderBy('name')->get(['id', 'name','photo'])->toArray();


        // Load positions for dropdown
//        $this->loadPositions();
    }

    public function loadPositions()
    {
        $this->positions = Position::orderBy('name')->get();
    }

    #[On('positionSelectize')]
    public function positionSelectize($values)
    {
        $current = is_array($this->selectedPositions) ? $this->selectedPositions : [];

        $this->selectedPositions = $values;
    }

    public function store()
    {
        $this->validate();

        try {
            $attemptCount = Attempt::whereDate('attempt_date', $this->attempt_date)->count();
            $this->attempt_nb = $attemptCount + 1;

            Attempt::create([
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
                'created_by' => Auth::id(),
            ]);




            session()->flash('message', 'Attempt recorded successfully!');



            // Reset form
            $this->reset([
                'successful', 'satisfaction_rating', 'hover_rating', 'description',
                'attempt_reason', 'attempt_time', 'duration_minutes',
                'lubrication_used', 'attempt_type', 'position_id'
            ]);

            return redirect()->route('attempts');

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while saving the attempt. Please try again.');
        }
    }

    public function cancel()
    {
        // Reset form
        $this->reset();
        $this->attempt_date = now()->format('Y-m-d');
        $this->loadPositions();

        // Redirect to attempts list
        return redirect()->route('attempts.index');
    }

    public function updated($propertyName)
    {
        // Real-time validation
        $this->validateOnly($propertyName);
    }

    public function setRating($rating)
    {
        $this->satisfaction_rating = $rating;
        $this->hover_rating = null;
    }

    public function hoverRating($rating)
    {
        $this->hover_rating = $rating;
    }

    public function resetHover()
    {
        $this->hover_rating = null;
    }

    public function resetRating()
    {
        $this->satisfaction_rating = null;
        $this->hover_rating = null;
    }

    public function render()
    {
        return view('livewire.attempts.attempt-add');
    }
}
