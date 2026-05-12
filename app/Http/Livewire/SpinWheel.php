<?php

namespace App\Http\Livewire;

use App\Models\Position;
use Livewire\Component;

class SpinWheel extends Component
{
    public $positions = [];

    public function mount()
    {
        $this->positions = Position::select('name','photo','description')
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'name'        => $p->name,
                'photo'       => $p->photo ? asset('storage/' . $p->photo) : null,
                'description' => $p->description,
            ])->values()->toArray();
    }

    public function render()
    {
        return view('livewire.spin_wheel', [
            'positions' => $this->positions
        ]);
    }
}
