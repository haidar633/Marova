<?php

namespace App\Http\Livewire\VibeChecks;

use App\Models\VibeCheck;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class VibeCheckAdd extends Component
{
    use AuthorizesRequests;

    public $status = 'Available for Connection';
    public $context;
    public $is_active = true;

    protected $rules = [
        'status' => 'required|in:Available for Connection,Recharging/Busy',
        'context' => 'nullable|string|max:500',
        'is_active' => 'boolean',
    ];

    public function store()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // If setting as active, deactivate all others
            if ($this->is_active) {
                VibeCheck::where('user_id', Auth::id())
                    ->update(['is_active' => false]);
            }

            VibeCheck::create([
                'user_id' => Auth::id(),
                'status' => $this->status,
                'context' => $this->context,
                'is_active' => $this->is_active,
            ]);

            DB::commit();

            session()->flash('success', 'Vibe check created successfully');
            return redirect()->route('vibe-checks');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error creating vibe check: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.vibe-checks.vibe-check-add');
    }
}
