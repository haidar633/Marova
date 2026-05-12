<?php

namespace App\Http\Livewire;

use App\Models\Masturbation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MasturbationTracker extends Component
{
    // Form Fields
    public $selectedDate;
    public $existingEntryId;
    public $entry_time;
    public $reason;
    public $rating;
    public $description;
    public $vaseline_used = false;
    public $count = 1;

    protected function rules()
    {
        return [
            'selectedDate' => 'required|date',
            'entry_time' => 'nullable|date_format:H:i',
            'reason' => 'nullable|string|max:1000',
            'rating' => 'nullable|numeric|min:0.5|max:5|regex:/^\d+(\.[05])?$/',
            'description' => 'nullable|string|max:2000',
            'vaseline_used' => 'boolean',
            'count' => 'required|integer|min:1',
        ];
    }

    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');

    }

    /**
     * Fetch entries and checkmarks for the calendar.
     */
    public function getEvents()
    {
        $entries = Masturbation::where('created_by', Auth::id())->get();

        $events = $entries->map(function ($entry) {
            return [
                'id' => 'entry-' . $entry->id,
                'title' => '', // Removed 'Entry (x6)'
                'start' => $entry->entry_date->format('Y-m-d'),
                'allDay' => true,
                'display' => 'background', // Color the whole day
                'backgroundColor' => '#dc3545', // Red
                'borderColor' => '#dc3545',
                'classNames' => ['masturbation-entry']
            ];
        });

        return $events->values()->all();
    }

    /**
     * Open the modal for a new entry or edit existing one if it exists on that date.
     */
    public function openModal($date)
    {
        $formattedDate = Carbon::parse($date)->format('Y-m-d');
        
        // Check if an entry already exists for this date
        $entry = Masturbation::where('created_by', Auth::id())
            ->where('entry_date', $formattedDate)
            ->first();

        if ($entry) {
            return $this->openExistingEntry($entry->id);
        }

        $this->reset(); // Resets all public properties
        $this->selectedDate = $formattedDate;
        $this->entry_time = now()->format('H:i');
        $this->dispatch('show-modal');
    }

    /**
     * Open the modal to edit an existing entry.
     */
    public function openExistingEntry($id)
    {
        $entry = Masturbation::findOrFail($id);

        $this->existingEntryId = $entry->id;
        $this->selectedDate = $entry->entry_date->format('Y-m-d');
        $this->entry_time = $entry->entry_time ? Carbon::parse($entry->entry_time)->format('H:i') : null;
        $this->reason = $entry->reason;
        $this->rating = $entry->rating;
        $this->description = $entry->description;
        $this->vaseline_used = $entry->vaseline_used;
        $this->count = $entry->count;

        $this->dispatch('show-modal');
    }

    /**
     * Save or update the entry.
     */
    public function saveEntry()
    {
        $validatedData = $this->validate();

        Masturbation::updateOrCreate(
            ['id' => $this->existingEntryId],
            [
                'created_by' => Auth::id(),
                'entry_date' => $this->selectedDate,
                'entry_time' => $validatedData['entry_time'],
                'reason' => $validatedData['reason'],
                'rating' => $validatedData['rating'],
                'description' => $validatedData['description'],
                'vaseline_used' => $validatedData['vaseline_used'],
                'count' => $validatedData['count'],
            ]
        );

        session()->flash('success', 'Entry saved successfully!');
        $this->dispatch('hide-modal-and-refetch');
    }

    public function render()
    {
        return view('livewire.masturbation-view');
    }
}
