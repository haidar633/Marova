<?php

namespace App\Http\Livewire\VibeChecks;

use App\Models\VibeCheck;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class VibeCheckView extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    protected $listeners = ['vibeCheckDeleted' => '$refresh', 'vibeCheckUpdated' => '$refresh'];

    public function mount()
    {
        // Ensure only one active vibe check exists
        $this->ensureSingleActiveVibeCheck();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    private function ensureSingleActiveVibeCheck()
    {
        $activeCount = VibeCheck::where('user_id', Auth::id())
            ->where('is_active', true)
            ->count();

        if ($activeCount > 1) {
            // Keep only the most recent one active
            VibeCheck::where('user_id', Auth::id())
                ->where('is_active', true)
                ->orderBy('updated_at', 'desc')
                ->skip(1)
                ->update(['is_active' => false]);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $vibeCheck = VibeCheck::where('user_id', Auth::id())->findOrFail($id);

            if ($vibeCheck->is_active) {
                // Deactivate this one
                $vibeCheck->update(['is_active' => false]);
            } else {
                // Deactivate all others and activate this one
                VibeCheck::where('user_id', Auth::id())
                    ->where('id', '!=', $id)
                    ->update(['is_active' => false]);

                $vibeCheck->update(['is_active' => true]);
            }

            $this->dispatch('vibeCheckUpdated');
            session()->flash('success', 'Vibe check status updated successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating vibe check: ' . $e->getMessage());
        }
    }

    #[On('destroy')]
    public function destroy($id)
    {
        try {
            $vibeCheck = VibeCheck::where('user_id', Auth::id())->findOrFail($id);
            $vibeCheck->delete();

            $this->dispatch('vibeCheckDeleted');
            session()->flash('success', 'Vibe check deleted successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting vibe check: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = VibeCheck::where('user_id', Auth::id());

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('status', 'like', '%' . $this->search . '%')
                    ->orWhere('context', 'like', '%' . $this->search . '%');
            });
        }

        $vibeChecks = $query->orderBy('is_active', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        $activeVibeCheck = VibeCheck::where('user_id', Auth::id())
            ->where('is_active', true)
            ->first();

        return view('livewire.vibe-checks.vibe-check-view', compact('vibeChecks', 'activeVibeCheck'));
    }
}
