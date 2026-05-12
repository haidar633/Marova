<?php

namespace App\Http\Livewire;

use App\Models\Appointment;
use App\Models\Attempt;
use App\Models\Position;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class SexTracking extends Component
{
    // Data Collections
    public $positionOptions = [];

    // Form Fields (Livewire Properties)
    public $selectedDate;
    public $existingAttemptId;
    public $successful = false;
    public $lubrication_used = false;
    public $attempt_reason;
    public $duration_minutes;
    public $attempt_time;
    public $attempt_type;
    public $selectedPositions = [];
    public $satisfaction_rating;
    public $description;

    public $her_rating;
    public $her_description;

    protected function rules()
    {
        return [
            'selectedDate' => 'required|date',
            'attempt_time' => 'nullable|date_format:H:i',
            'successful' => 'boolean',
            'lubrication_used' => 'boolean',
            'satisfaction_rating' => 'nullable|numeric|min:0.5|max:5|regex:/^\d+(\.[05])?$/',
            'description' => 'nullable|string|max:2000',
            'attempt_reason' => 'nullable|string|max:1000',
            'duration_minutes' => 'nullable|integer|min:0',
            'attempt_type' => 'nullable|string',
            'selectedPositions' => 'nullable|array',
            'her_rating' => 'nullable|numeric|min:0|max:5|regex:/^\d+(\.[05])?$/',
            'her_description' => 'nullable|string',

        ];
    }

    public function mount()
    {
        // Pre-load position data for the Selectize dropdown
        $this->positionOptions = Position::orderBy('name')->get(['id', 'name', 'photo'])->toArray();
        $this->selectedDate = now()->format('Y-m-d');

    }


    public function openExistingAttempt($id)
    {
        $this->resetValidation();
        $this->resetExcept('positionOptions');

        $attempt = Attempt::findOrFail($id);

        $this->existingAttemptId = $attempt->id;
        $this->selectedDate = $attempt->attempt_date;
        $this->successful = (bool)$attempt->successful;
        $this->lubrication_used = (bool)$attempt->lubrication_used;
        $this->satisfaction_rating = $attempt->satisfaction_rating;
        $this->description = $attempt->description;
        $this->attempt_reason = $attempt->attempt_reason;
        $this->attempt_time = $attempt->attempt_time ? Carbon::parse($attempt->attempt_time)->format('H:i') : null;
        $this->duration_minutes = $attempt->duration_minutes;
        $this->attempt_type = $attempt->attempt_type;
        $this->selectedPositions = json_decode($attempt->positions, true) ?? [];
        $this->her_rating = $attempt->her_rating;
        $this->her_description = $attempt->her_description;

        $this->dispatch('show-modal');
    }


    public function getEvents()
    {
        $attempts = Attempt::where('created_by', Auth::id())->get();

        // Map existing attempts to calendar events
        $events = $attempts->map(function ($attempt) {
            return [
                'id' => 'attempt-' . $attempt->id,
                'title' => '', // Removed 'Success/Attempt' text
                'start' => $attempt->attempt_date,
                'allDay' => true,
                'display' => 'background', // Color the whole day
                'backgroundColor' => $attempt->successful ? '#28a745' : '#dc3545',
                'borderColor' => $attempt->successful ? '#28a745' : '#dc3545',
                'classNames' => [$attempt->successful ? 'sex-success' : 'sex-failure'],
            ];
        });

        return $events->values()->all();
    }

    /**
     * Open the modal and load data if an attempt exists for the selected date.
     */
    public function openModal($date)
    {
        $formattedDate = Carbon::parse($date)->format('Y-m-d');
        
        // Check if an attempt already exists for this date
        $attempt = Attempt::where('created_by', Auth::id())
            ->where('attempt_date', $formattedDate)
            ->first();

        if ($attempt) {
            return $this->openExistingAttempt($attempt->id);
        }

        $this->resetValidation();
        $this->resetExcept('positionOptions'); // Keep select options

        $this->existingAttemptId = null; // Reset to create a new one
        $this->selectedDate = $formattedDate;
        $this->successful = false;
        $this->lubrication_used = false;
        $this->satisfaction_rating = null;
        $this->description = null;
        $this->attempt_reason = null;
        $this->attempt_time = null;
        $this->duration_minutes = null;
        $this->attempt_type = null;
        $this->selectedPositions = [];
        $this->attempt_time = now()->format('H:i');

        $this->dispatch('show-modal');
    }

    /**
     * Save the attempt and create/update the corresponding appointment.
     */
    public function saveAttempt()
    {
        $validatedData = $this->validate();

        DB::transaction(function () use ($validatedData) {
            // Determine the attempt number for the day
            $attemptCount = Attempt::whereDate('attempt_date', $this->selectedDate)->where('created_by', Auth::id())->count();

            $attemptData = [
                'successful' => $validatedData['successful'],
                'satisfaction_rating' => $validatedData['satisfaction_rating'],
                'description' => $validatedData['description'],
                'attempt_reason' => $validatedData['attempt_reason'],
                'attempt_time' => $validatedData['attempt_time'],
                'attempt_date' => $this->selectedDate,
                'duration_minutes' => $validatedData['duration_minutes'],
                'lubrication_used' => $validatedData['lubrication_used'],
                'attempt_type' => $validatedData['attempt_type'],
                'her_rating' => $this->her_rating,
                'her_description' => $this->her_description,
                'positions' => json_encode($this->selectedPositions ?? []),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            // Use updateOrCreate to handle both new and existing attempts
            $attempt = Attempt::updateOrCreate(['id' => $this->existingAttemptId], $attemptData);

            // Set attempt number if it's a new record
            if ($attempt->wasRecentlyCreated) {
                $attempt->attempt_nb = $attemptCount + 1;
                $attempt->save();
            }

            // Also create or update the linked appointment record
            $startDateTime = Carbon::parse($this->selectedDate . ' ' . ($this->attempt_time ?? '00:00:00'));
            Appointment::updateOrCreate(
                ['attempt_id' => $attempt->id],
                [
                    'sales_id' => Auth::id(), // Or other relevant ID
                    'parent_id' => Auth::id(), // Or other relevant ID
                    'scheduled_date' => $this->selectedDate,
                    'start_date' => $startDateTime,
//                    'end_date' => $startDateTime->copy()->addMinutes($this->duration_minutes ?? 30),
                    'status' => $this->successful ? 'done' : 'cancelled',
                    'staff_note' => 'Attempt logged via calendar.',
                    'sales_note' => $this->description,
                ]
            );
        });

        session()->flash('success', 'Attempt saved successfully!');
        $this->dispatch('hide-modal-and-refetch');
    }
    #[On('positionSelectize')]

    public function positionSelectize($values)
    {
        $this->selectedPositions = $values;
    }

    public function deleteAttempt($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // Find the attempt
                $attempt = Attempt::where('id', $id)
                    ->where('created_by', Auth::id()) // Ensure user can only delete their own attempts
                    ->firstOrFail();

                // Delete associated appointment
                Appointment::where('attempt_id', $id)->delete();

                // Delete the attempt
                $attempt->delete();
            });

            session()->flash('success', 'Attempt deleted successfully!');
            $this->dispatch('hide-modal-and-refetch');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete attempt. Please try again.');
            \Log::error('Attempt deletion failed: ' . $e->getMessage());
        }
    }

    // Keep the old destroy method for backward compatibility if needed
    #[On('destroy')]
    public function destroy($id)
    {
        $this->deleteAttempt($id);
    }


    public function render()
    {
        return view('livewire.sex-tracking');
    }
}
