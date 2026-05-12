<?php

namespace App\Http\Livewire\Partners;

use App\Models\Partner;
use App\Services\MediaManagementService;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;
use Storage;

class PartnerEdit extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public $partnerId;
    public $partner;
    public $name;
    public $image;
    public $existingImage;
    public $newImageUploaded = false;
    public $star_rating;
    public $description;
    public $last_day_we_met;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'star_rating' => 'nullable|numeric|min:0.5|max:5|regex:/^\d+(\.[05])?$/',
            'description' => 'nullable|string',
            'last_day_we_met' => 'nullable|date',
        ];
    }

    public function mount($id)
    {
        $this->authorize('partner-edit');

        $this->partnerId = $id;
        $this->partner = Partner::findOrFail($id);

        // Populate existing data
        $this->name = $this->partner->name;
        $this->star_rating = $this->partner->star_rating;
        $this->description = $this->partner->description;
        $this->last_day_we_met = $this->partner->last_day_we_met ? Carbon::parse($this->partner->last_day_we_met)->format('Y-m-d') : null;
        $this->existingImage = $this->partner->image;
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'image|max:2048',
        ]);

        $this->newImageUploaded = true;
    }

    public function updatedLastDayWeMet($value)
    {
        // This method ensures the field is properly updated and triggers re-render
        $this->validateOnly('last_day_we_met');
        // Force re-render to update the time passed display
        $this->dispatch('$refresh');
    }

    public function removeImage()
    {
        if ($this->existingImage) {
            // Delete the existing image file
            if (Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->exists($this->existingImage)) {
                Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->delete($this->existingImage);
            }

            $this->existingImage = null;
            $this->partner->update(['image' => null]);

            session()->flash('success', 'Image removed successfully.');
        }
    }

    public function update()
    {
        $validatedData = $this->validate();

        try {
            DB::beginTransaction();

            $imagePath = $this->existingImage; // Keep existing image by default

            // Handle new image upload
            if ($this->image && $this->newImageUploaded) {
                // Delete old image if exists
                if ($this->existingImage && Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->exists($this->existingImage)) {
                    Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->delete($this->existingImage);
                }

                // Upload new image
                $imagePath = MediaManagementService::uploadMedia(
                    $this->image,
                    '/partners',
                    env('FILESYSTEM_DRIVER', 'public'),
                    explode('.', $this->image->getClientOriginalName())[0] . '_' . time() . rand(0, 999999999999) . '.' . $this->image->getClientOriginalExtension()
                );
            }

            // Update partner
            $this->partner->update([
                'name' => $this->name,
                'image' => $imagePath,
                'star_rating' => $this->star_rating,
                'description' => $this->description,
                'last_day_we_met' => $this->last_day_we_met,
            ]);

            DB::commit();

            return redirect()->route('partners');

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error updating partner', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'partner_id' => $this->partnerId
            ]);
            session()->flash('error', 'Error updating partner: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.partners.partner-edit');
    }
}
