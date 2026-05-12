<?php

namespace App\Http\Livewire\Doctors;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class DoctorAdd extends Component
{
    public $contactInfos = [];
    public $services = [];
    public $fname;
    public $lname;
    public $date_of_birth;
    public $address;
    public $password;
    public $email;
    public $password_confirmation;
    public $serial_nb;

    public $doctor_color;


    public $dr_percentage;
    public $speciality;

    public $availability = [
        'monday' => ['checked' => false, 'start_time' => '08:00', 'end_time' => '16:00'],
        'tuesday' => ['checked' => false, 'start_time' => '08:00', 'end_time' => '16:00'],
        'wednesday' => ['checked' => false, 'start_time'=> '08:00', 'end_time' => '16:00'],
        'thursday' => ['checked' => false, 'start_time'=> '08:00', 'end_time' => '16:00'],
        'friday' => ['checked' => false, 'start_time'=> '08:00', 'end_time' => '16:00'],
        'saturday' => ['checked' => false, 'start_time'=> '08:00', 'end_time' => '16:00'],
        'sunday' => ['checked' => false, 'start_time'=> '08:00', 'end_time' => '16:00']
    ];

    protected $rules = [
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'date_of_birth' => 'required|date',
        'address' => 'required|string|max:255',
        'email' => 'required|string|email|unique:users,email',
        'password' => 'required',
        'password_confirmation' => 'required|same:password',
        'serial_nb' => 'nullable|string|max:255|unique:users,serial_nb',
        'contactInfos.*.type' => 'required|string|in:phone,whatsapp',
        'contactInfos.*.value' => 'required|string',
        'services.*.description' => 'required|string|max:255',
        'services.*.price' => 'required|numeric|min:0',
        'availability.*.checked' => 'boolean',
        'availability.*.start_time' => 'required_if:availability.*.checked,true|date_format:H:i',
        'availability.*.end_time' => 'required_if:availability.*.checked,true|date_format:H:i|after:availability.*.start_time',
        'dr_percentage' => 'nullable|numeric|min:0|max:100',
        'speciality' => 'nullable|string|max:255',
        'doctor_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',

    ];


    protected $messages = [
        'availability.*.start_time.required_if' => 'Start time is required when day is selected.',
        'availability.*.end_time.required_if' => 'End time is required when day is selected.',
        'availability.*.end_time.after' => 'End time must be after start time.',
        'services.*.description.required' => 'Service description is required.',
        'services.*.price.required' => 'Service price is required.',
        'services.*.price.numeric' => 'Service price must be a number.',
        'services.*.price.min' => 'Service price must be at least 0.',
    ];

    public function mount()
    {
        $this->authorize('doctor-create');
        $this->addContactInfo();
        $this->addService();
    }

    public function addContactInfo(){
        if (empty($this->contactInfos)) {
            $this->contactInfos[] = [
                'type' => 'phone',
                'value' => ''
            ];
        } else {
            $this->contactInfos[] = [
                'type' => '',
                'value' => ''
            ];
        }
    }

    private function validateAtLeastOneAvailableDay()
    {
        foreach ($this->availability as $day => $schedule) {
            if ($schedule['checked']) {
                return true;
            }
        }

        throw new \Exception('Please set at least one day of availability.');
    }

    public function removeContactInfo($index)
    {
        unset($this->contactInfos[$index]);
        $this->contactInfos = array_values($this->contactInfos);
    }

    public function addService()
    {
        $this->services[] = [
            'description' => '',
            'price' => ''
        ];
    }

    public function removeService($index)
    {
        unset($this->services[$index]);
        $this->services = array_values($this->services);
    }

    public function store()
    {
        $this->validate();
        $this->dispatch('scrollToElement');

        try {

            DB::beginTransaction();

            // Create doctor
            $doctor = User::create([
                'fname' => $this->fname,
                'lname' => $this->lname,
                'email' => $this->email,
                'type' => 'Doctor',
                'date_of_birth' => $this->date_of_birth,
                'address' => $this->address,
                'serial_nb' => $this->serial_nb,
                'password' => $this->password,
            ]);

            $doctor->userDetail()->create([
                'user_id' => $doctor->id,
                'speciality' => $this->speciality,
                'dr_percentage' => $this->dr_percentage,
                'center_percentage' => (100-$this->dr_percentage),
                'color' => $this->doctor_color ?? '#000000', // default to black if not selected
            ]);

            $doctorRole = Role::where('name', 'Doctor')->firstOrFail();
            $doctor->assignRole($doctorRole);

            // Save contact info
            $validContactInfos = array_filter($this->contactInfos, function($info) {
                return !empty($info['type']) && !empty($info['value']);
            });

            foreach ($validContactInfos as $info) {
                $doctor->contactDetail()->create([
                    'type' => $info['type'],
                    'value' => $info['value']
                ]);
            }

            // Save services
            $validServices = array_filter($this->services, function($service) {
                return !empty($service['description']) && $service['price'] !== '';
            });

            foreach ($validServices as $service) {
                $doctor->services()->create([
                    'description' => $service['description'],
                    'price' => $service['price']
                ]);
            }

            // Save availability
            $hasAvailability = false;
            foreach ($this->availability as $day => $schedule) {
                if ($schedule['checked']) {
                    if (empty($schedule['start_time']) || empty($schedule['end_time'])) {
                        throw new \Exception("Please set both start and end time for {$day}.");
                    }

                    $doctor->availability()->create([
                        'day' => $day,
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time']
                    ]);
                    $hasAvailability = true;
                }
            }

            $this->validateAtLeastOneAvailableDay();


            DB::commit();

            session()->flash('success', 'Doctor created successfully.');
            return redirect()->route('doctors');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating doctor: ' . $e->getMessage());
            session()->flash('error', $e->getMessage());
        }
    }

    public function updatedFname()
    {
        $this->generateSerialNumber();
    }

    public function updatedLname()
    {
        $this->generateSerialNumber();
    }

    private function generateSerialNumber()
    {
        if (empty($this->fname) && empty($this->lname)) {
            return;
        }

        $prefix = !empty($this->fname) && !empty($this->lname)
            ? strtoupper(substr($this->fname, 0, 1) . substr($this->lname, 0, 1))
            : strtoupper(substr($this->fname ?: $this->lname, 0, 2));

        do {
            $randomNumbers = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            $serialNb = $prefix . $randomNumbers;
        } while (User::where('serial_nb', $serialNb)->exists());

        $this->serial_nb = $serialNb;
    }

    public function getPhoneLabel($index)
    {
        $phoneCount = 0;
        for ($i = 0; $i <= $index; $i++) {
            if (isset($this->contactInfos[$i]) && $this->contactInfos[$i]['type'] === 'phone') {
                $phoneCount++;
            }
        }
        return $phoneCount;
    }

    public function render()
    {
        return view('livewire.Doctors.doctor-add');
    }
}
