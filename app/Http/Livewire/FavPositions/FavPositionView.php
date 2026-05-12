<?php

namespace App\Http\Livewire\FavPositions;

use App\Models\Position;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class FavPositionView extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    // Updated listeners array to match the event name
    protected $listeners = ['removeFavoriteConfirmed' => 'removeFavorite'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->authorize('position-list');
    }

    public function removeFavorite($id)
    {
        try {
            $position = Position::findOrFail($id);
            $position->update(['is_favorite' => false]);

            $this->render();

        } catch (\Exception $e) {
            session()->flash('error', 'Error removing from favorites: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Position::where('is_favorite', true);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $favPositions = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.FavPositions.favposition-view', compact('favPositions'));
    }
}
