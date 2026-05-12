<?php

namespace App\Http\Livewire\Doctors;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class DoctorEdit extends Component
{
    public $doctor;
    public $contactInfos = [];
    public $services = []; // New property for services
    public $fname;
    public $lname;
    public $date_of_birth;
    public $address;
    public $email;
    public $serial_nb;
    public $password;
    public $password_confirmation;

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
        'fname' => 'required|string',
        'lname' => 'required|string',
        'date_of_birth' => 'required|date',
        'address' => 'required|string',
        'email' => 'required|email',
        'serial_nb' => 'nullable|string',
        'password' => 'nullable',
        'password_confirmation' => 'nullable|same:password',
        'contactInfos.*.type' => 'required|string|in:phone,whatsapp',
        'contactInfos.*.value' => 'required|string',
        'services.*.description' => 'required|string',
        'services.*.price' => 'required|numeric|min:0', // Validation for service price
        'availability.*.checked' => 'boolean',
        'availability.*.start_time' => 'required_if:availability.*.checked,true|date_format:H:i',
        'availability.*.end_time' => 'required_if:availability.*.checked,true|date_format:H:i|after:availability.*.start_time',
        'dr_percentage' => 'nullable|numeric|min:0|max:100',
        'speciality' => 'nullable|string',
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

    public function mount($id)
    {
        $this->authorize('doctor-edit');
        $this->doctor = User::with(['contactDetail', 'userDetail', 'roles', 'availability', 'services'])->findOrFail($id);
        $this->loadDoctorData();
    }

    private function loadDoctorData()
    {
        $this->fname = $this->doctor->fname;
        $this->lname = $this->doctor->lname;
        $this->date_of_birth = $this->doctor->date_of_birth;
        $this->address = $this->doctor->address;
        $this->email = $this->doctor->email;
        $this->serial_nb = $this->doctor->serial_nb;
        $this->doctor_color = optional($this->doctor->userDetail)->color ?? '#000000';


        // Load contact information
        $this->contactInfos = $this->doctor->contactDetail()
            ->get()
            ->map(function($contact) {
                return [
                    'type' => $contact->type,
                    'value' => $contact->value
                ];
            })
            ->toArray();

        // Load services information
        $this->services = $this->doctor->services()
            ->get()
            ->map(function($service) {
                return [
                    'description' => $service->description,
                    'price' => $service->price
                ];
            })
            ->toArray();

        // Get user detail information directly
        $userDetail = $this->doctor->userDetail;
        if ($userDetail) {
            $this->speciality = $userDetail->speciality;
            $this->dr_percentage = $userDetail->dr_percentage;
        }

        if (empty($this->contactInfos)) {
            $this->addContactInfo();
        }

        if (empty($this->services)) {
            $this->addService();
        }

        // Load availability
        $doctorAvailability = $this->doctor->availability()->get();
        foreach ($doctorAvailability as $schedule) {
            $this->availability[$schedule->day] = [
                'checked' => true,
                'start_time' => date('H:i', strtotime($schedule->start_time)),
                'end_time' => date('H:i', strtotime($schedule->end_time)),
            ];
        }
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

    private function nullifyAppointmentsOnUnavailableDays($doctorId, $newAvailableDays)
    {
        $allAppointments = \App\Models\DutyScheduling::where('doctor_id', $doctorId)
            ->whereDate('start_date', '>=', now())
            ->get();

        foreach ($allAppointments as $appointment) {
            $dayOfWeek = \Carbon\Carbon::parse($appointment->start_date)->format('l');

            if (!in_array(strtolower($dayOfWeek), $newAvailableDays)) {
                $appointment->doctor_id = null;
                $appointment->save();
            }
        }
    }


    public function removeContactInfo($index)
    {
        unset($this->contactInfos[$index]);
        $this->contactInfos = array_values($this->contactInfos);
    }

    // New method to add service
    public function addService()
    {
        $this->services[] = [
            'description' => '',
            'price' => ''
        ];
    }

    // New method to remove service
    public function removeService($index)
    {
        unset($this->services[$index]);
        $this->services = array_values($this->services);
    }

    public function update()
    {
        $this->validate();
        $this->dispatch('scrollToElement');

        try {
//            // Modify email unique validation if email hasn't changed
//            if ($this->email === $this->doctor->email) {
//                $this->rules['email'] = 'required|string|email';
//            } else {
//                $this->rules['email'] = 'required|string|email|unique:users,email,' . $this->doctor->id;
//            }

            // Same for serial number
            if ($this->serial_nb === $this->doctor->serial_nb) {
                $this->rules['serial_nb'] = 'nullable|string';
            } else {
                $this->rules['serial_nb'] = 'nullable|string|unique:users,serial_nb,' . $this->doctor->id;
            }

            $availableDays = array_keys(array_filter($this->availability, function ($day) {
                return $day['checked'];
            }));

            $this->nullifyAppointmentsOnUnavailableDays($this->doctor->id, $availableDays);


            DB::beginTransaction();

            // Update basic information
            $updateData = [
                'fname' => $this->fname,
                'lname' => $this->lname,
                'email' => $this->email,
                'date_of_birth' => $this->date_of_birth,
                'address' => $this->address,
                'serial_nb' => $this->serial_nb,
            ];

            // Only update password if provided
            if (!empty($this->password)) {
                $updateData['password'] = $this->password;
            }

            $this->doctor->update($updateData);

            // Update contact information
            $this->doctor->contactDetail()->delete(); // Remove existing contacts
            $validContactInfos = array_filter($this->contactInfos, function($info) {
                return !empty($info['type']) && !empty($info['value']);
            });

            foreach ($validContactInfos as $info) {
                $this->doctor->contactDetail()->create([
                    'type' => $info['type'],
                    'value' => $info['value']
                ]);
            }

            // Update services
            $this->doctor->services()->delete(); // Remove existing services
            $validServices = array_filter($this->services, function($service) {
                return !empty($service['description']) && $service['price'] !== '';
            });

            foreach ($validServices as $service) {
                $this->doctor->services()->create([
                    'description' => $service['description'],
                    'price' => $service['price']
                ]);
            }

            // Update user details - First check if it exists
            if ($this->doctor->userDetail) {
                $this->doctor->userDetail->update([
                    'speciality' => $this->speciality,
                    'dr_percentage' => $this->dr_percentage,
                    'center_percentage' => (100 - $this->dr_percentage)
                ]);
            } else {
                // Create if it doesn't exist
                $this->doctor->userDetail()->create([
                    'speciality' => $this->speciality,
                    'dr_percentage' => $this->dr_percentage,
                    'center_percentage' => (100 - $this->dr_percentage)
                ]);
            }

            // Update availability
            $this->doctor->availability()->delete(); // Remove existing availability
            $hasAvailability = false;
            foreach ($this->availability as $day => $schedule) {
                if ($schedule['checked']) {
                    if (empty($schedule['start_time']) || empty($schedule['end_time'])) {
                        throw new \Exception("Please set both start and end time for {$day}.");
                    }

                    $this->doctor->availability()->create([
                        'day' => $day,
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time']
                    ]);
                    $hasAvailability = true;
                }
            }

            if (!$hasAvailability) {
                throw new \Exception('Please set at least one day of availability.');
            }

            \DB::table('user_details')->updateOrInsert(
                ['user_id' => $this->doctor->id],
                ['color' => $this->doctor_color]
            );

            DB::commit();

            session()->flash('success', 'Doctor updated successfully.');
            return redirect()->route('doctors');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating doctor: ' . $e->getMessage());
            session()->flash('error', $e->getMessage());
        }
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

        $prefix = '';

        if (!empty($this->fname) && !empty($this->lname)) {
            $prefix = strtoupper(substr($this->fname, 0, 1) . substr($this->lname, 0, 1));
        } elseif (!empty($this->fname)) {
            $prefix = strtoupper(substr($this->fname, 0, 2));
        }

        if ($prefix) {
            $randomNumbers = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            $this->serial_nb = $prefix . $randomNumbers;
        }
    }


    public function render()
    {
        return view('livewire.Doctors.doctor-edit');
    }
}
