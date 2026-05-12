<?php

namespace App\Http\Livewire\Positions;

use App\Models\Position;
use App\Services\MediaManagementService;
use DB;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class PositionAdd extends Component
{
    use AuthorizesRequests,WithFileUploads;

    public $notBooted = true;
    public $name;
    public $photo;
    public $description;
    public $newPhotoUploaded = false;


    protected $rules = [
        'name' => 'required|string|max:255',
        'photo' => 'nullable|image',
        'description' => 'nullable|string',

    ];

    public function mount()
    {
        $this->authorize('position-create');

    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image',
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
                    '/positions',
                    env('FILESYSTEM_DRIVER', 'public'),
                    explode('.', $this->photo->getClientOriginalName())[0] . '_' . time() . rand(0, 999999999999) . '.' . $this->photo->getClientOriginalExtension()
                );
            }

            $user = Position::create([
                'name' => $this->name,
                'photo' => $photoPath,
                'description' => $this->description,

            ]);


            DB::commit();


            return redirect()->route('positions');


        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error creating intimacy item: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.Positions.position-add');
    }
}
