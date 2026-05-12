<?php

namespace App\Http\Livewire\Partners;

use App\Models\Partner;
use App\Services\MediaManagementService;
use DB;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class PartnerAdd extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public $name;
    public $image;
    public $star_rating;
    public $description;
    public $last_day_we_met;
    public $newImageUploaded = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'image' => 'required|image',
        'star_rating' => 'nullable|numeric|min:0.5|max:5|regex:/^\d+(\.[05])?$/',
        'description' => 'nullable|string',
        'last_day_we_met' => 'nullable|date',
    ];

    public function mount()
    {
        $this->authorize('partner-create');
        $this->last_day_we_met = null;
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

    public function store()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $imagePath = null;
            if ($this->image && $this->newImageUploaded) {
                $imagePath = MediaManagementService::uploadMedia(
                    $this->image,
                    '/partners',
                    env('FILESYSTEM_DRIVER', 'public'),
                    explode('.', $this->image->getClientOriginalName())[0] . '_' . time() . rand(0, 999999999999) . '.' . $this->image->getClientOriginalExtension()
                );
            }

            Partner::create([
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
            session()->flash('error', 'Error creating partner: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.partners.partner-add');
    }
}
