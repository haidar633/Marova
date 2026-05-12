<?php

namespace App\Http\Livewire\Positions;

use App\Models\Position;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;


class PositionView extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    protected $listeners = ['positionDeleted' => '$refresh', 'positionFavorited' => '$refresh'];

    // Reset pagination when search changes
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->authorize('position-list');
    }

    #[On('destroy')]
    public function destroy($id)
    {
        try {
            $position = Position::findOrFail($id);

            if ($position->photo && \Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->exists($position->photo)) {
                \Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->delete($position->photo);
            }

            $position->delete();

            $this->dispatch('positionDeleted');
            session()->flash('success', 'Intimacy item deleted successfully');

        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting intimacy item: ' . $e->getMessage());
        }
    }

    public function toggleFavorite($id)
    {
        try {
            $position = Position::findOrFail($id);
            $position->update(['is_favorite' => !$position->is_favorite]);

            $this->dispatch('positionFavorited');

        } catch (\Exception $e) {
            session()->flash('error', 'Error updating favorite status: ' . $e->getMessage());
        }
    }




    public function goToFavorites()
    {
        return redirect()->route('favorite-positions');
    }



    public function render()
    {
        $query = Position::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $positions = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.positions.position-view', compact('positions'));
    }
}
