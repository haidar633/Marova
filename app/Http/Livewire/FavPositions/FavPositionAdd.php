<?php

namespace App\Http\Livewire\FavPositions;

use App\Models\FavPosition;
use App\Services\MediaManagementService;
use DB;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class FavPositionAdd extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public $name;
    public $photo;
    public $description;
    public $newPhotoUploaded = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'photo' => 'nullable|image|max:2048',
        'description' => 'nullable|string',
    ];

    public function mount()
    {
        $this->authorize('position-create'); // Adjust permission as needed
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:2048',
        ]);

        $this->newPhotoUploaded = true;
    }

    public function store()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $photoPath = null;
            if ($this->photo && $this->newPhotoUploaded) {
                $photoPath = MediaManagementService::uploadMedia(
                    $this->photo,
                    '/favorite-positions',
                    env('FILESYSTEM_DRIVER', 'public'),
                    explode('.', $this->photo->getClientOriginalName())[0] . '_' . time() . rand(0, 999999999999) . '.' . $this->photo->getClientOriginalExtension()
                );
            }

           FavPosition::create([
                'name' => $this->name,
                'photo' => $photoPath,
                'description' => $this->description,
            ]);

            DB::commit();

            return redirect()->route('favorite-positions')->with('success', 'Favorite position created successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error creating favorite position', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Error creating favorite position: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.favorite-positions.favorite-position-add');
    }
}
