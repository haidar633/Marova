<?php

namespace App\Http\Livewire\FavoriteHubs;

use App\Models\Expenses;
use App\Models\FavoriteHub;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FavoriteHubAdd extends Component
{
    use AuthorizesRequests;


    public $link;
    public $description;

    protected $rules = [
        'link' => 'required|string',
        'description' => 'required|string',

    ];

    public function mount()
    {
        $this->authorize('favorate-hub-create');
    }

    public function store()
    {
        $this->dispatch('scrollToElement');

        $this->validate();

        try {
            FavoriteHub::create([
                'link' => $this->link,
                'description' => $this->description,
            ]);

            $this->dispatch('saved');

            session()->flash('success', 'Favorite Hub created successfully.');
            return redirect()->route('favorite-hubs');
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating favorite hub. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.FavoriteHubs.favorite-hub-add');
    }
}
