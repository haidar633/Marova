<?php

namespace App\Http\Livewire\FavPositions;

use App\Models\FavPosition;
use App\Services\MediaManagementService;
use DB;
use Exception;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;


class FavPositionEdit extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public $favPositionId;
    public $favPosition;
    public $name;
    public $photo;
    public $description;
    public $existingPhoto;
    public $newPhotoUploaded = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
        ];
    }

    public function mount($id)
    {
        $this->authorize('position-edit'); // Adjust permission as needed

        $this->favPositionId = $id;
        $this->favPosition = FavPosition::findOrFail($id);

        // Populate existing data
        $this->name = $this->favPosition->name;
        $this->description = $this->favPosition->description;
        $this->existingPhoto = $this->favPosition->photo;
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:2048',
        ]);

        $this->newPhotoUploaded = true;
    }

    public function removePhoto()
    {
        if ($this->existingPhoto) {
            // Delete the existing photo file
            if (Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->exists($this->existingPhoto)) {
                Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->delete($this->existingPhoto);
            }

            $this->existingPhoto = null;
            $this->favPosition->update(['photo' => null]);

            session()->flash('success', 'Photo removed successfully.');
        }
    }

    public function update()
    {
        $validatedData = $this->validate();

        try {
            DB::beginTransaction();

            $photoPath = $this->existingPhoto; // Keep existing photo by default

            // Handle new photo upload
            if ($this->photo && $this->newPhotoUploaded) {
                // Delete old photo if exists
                if ($this->existingPhoto && Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->exists($this->existingPhoto)) {
                    Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->delete($this->existingPhoto);
                }

                // Upload new photo
                $photoPath = MediaManagementService::uploadMedia(
                    $this->photo,
                    '/favorite-positions',
                    env('FILESYSTEM_DRIVER', 'public'),
                    explode('.', $this->photo->getClientOriginalName())[0] . '_' . time() . rand(0, 999999999999) . '.' . $this->photo->getClientOriginalExtension()
                );
            }

            // Update favorite position
            $this->favPosition->update([
                'name' => $this->name,
                'photo' => $photoPath,
                'description' => $this->description,
            ]);

            DB::commit();

            session()->flash('success', 'Favorite position updated successfully.');
            return redirect()->route('favorite-positions');

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error updating favorite position', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'favorite_position_id' => $this->favPositionId
            ]);
            session()->flash('error', 'Error updating favorite position: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.favorite-positions.favorite-position-edit');
    }
}
