<?php

namespace App\Http\Livewire\VibeChecks;

use App\Models\VibeCheck;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class VibeCheckEdit extends Component
{
    use AuthorizesRequests;

    public $vibeCheck;
    public $status;
    public $context;
    public $is_active;

    protected $rules = [
        'status' => 'required|in:Available for Connection,Recharging/Busy',
        'context' => 'nullable|string|max:500',
        'is_active' => 'boolean',
    ];

    public function mount($id)
    {
        $this->vibeCheck = VibeCheck::where('user_id', Auth::id())->findOrFail($id);
        $this->status = $this->vibeCheck->status;
        $this->context = $this->vibeCheck->context;
        $this->is_active = $this->vibeCheck->is_active;
    }

    public function update()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // If setting as active, deactivate all others
            if ($this->is_active && !$this->vibeCheck->is_active) {
                VibeCheck::where('user_id', Auth::id())
                    ->where('id', '!=', $this->vibeCheck->id)
                    ->update(['is_active' => false]);
            }

            $this->vibeCheck->update([
                'status' => $this->status,
                'context' => $this->context,
                'is_active' => $this->is_active,
            ]);

            DB::commit();

            session()->flash('success', 'Vibe check updated successfully');
            return redirect()->route('vibe-checks');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error updating vibe check: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.vibe-checks.vibe-check-edit');
    }
}
