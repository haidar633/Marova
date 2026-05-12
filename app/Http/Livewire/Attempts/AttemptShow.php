<?php

namespace App\Http\Livewire\Attempts;

use App\Models\Attempt;
use App\Models\Position;
use Livewire\Component;

class AttemptShow extends Component
{
    public Attempt $attempt;
    public array $positionDetails = [];

    public function mount($id)
    {
        // Find the attempt, eager-loading creator/updater relationships
        $this->attempt = Attempt::with(['creator', 'updater'])->findOrFail($id);

        // Ensure the authenticated user owns this attempt
        if ($this->attempt->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Decode the JSON string of position IDs
        $positionIds = json_decode($this->attempt->positions, true);

        // If there are position IDs, fetch their full details
        if (!empty($positionIds)) {
            $this->positionDetails = Position::whereIn('id', $positionIds)->get()->toArray();
        }
    }

    public function render()
    {
        return view('livewire.attempts.attempt-show');
    }
}
