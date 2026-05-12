<?php

namespace App\Http\Livewire\Positions;

use App\Models\Position;
use App\Services\MediaManagementService;
use DB;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;
use Storage;

class PositionEdit extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public $positionId;
    public $position;
    public $name;
    public $photo;
    public $existingPhoto;
    public $newPhotoUploaded = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image',
        ];
    }

    public function mount($id)
    {
        $this->authorize('position-edit'); // Adjust permission as needed

        $this->positionId = $id;
        $this->position = Position::findOrFail($id);

        // Populate existing data
        $this->name = $this->position->name;
        $this->existingPhoto = $this->position->photo;
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
            $this->position->update(['photo' => null]);

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
                    '/positions',
                    env('FILESYSTEM_DRIVER', 'public'),
                    explode('.', $this->photo->getClientOriginalName())[0] . '_' . time() . rand(0, 999999999999) . '.' . $this->photo->getClientOriginalExtension()
                );
            }

            // Update position
            $this->position->update([
                'name' => $this->name,
                'photo' => $photoPath,
            ]);

            DB::commit();

            return redirect()->route('positions');

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error updating position', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'position_id' => $this->positionId
            ]);
            session()->flash('error', 'Error updating position: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.positions.position-edit');
    }
}
