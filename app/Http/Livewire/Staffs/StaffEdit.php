<?php

namespace App\Http\Livewire\Staffs;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class StaffEdit extends Component
{
    public $staff;
    public $contactInfos = [];
    public $fname;
    public $lname;
    public $date_of_birth;
    public $address;
    public $email;
    public $serial_nb;
    public $password;
    public $password_confirmation;



    protected $rules = [
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'date_of_birth' => 'required|date',
        'address' => 'required|string|max:255',
        'email' => 'required|email',
        'serial_nb' => 'nullable|string|max:255',
        'password' => 'nullable',
        'password_confirmation' => 'nullable|same:password',
        'contactInfos.*.type' => 'required|string|in:phone,whatsapp',
        'contactInfos.*.value' => 'required|string',
    ];


    public function mount($id)
    {
        $this->authorize('staff-edit');
        $this->staff = User::with(['contactDetail', 'roles'])->findOrFail($id);
        $this->loadStaffData();
    }

    private function loadStaffData()
    {
        $this->fname = $this->staff->fname;
        $this->lname = $this->staff->lname;
        $this->date_of_birth = $this->staff->date_of_birth;
        $this->address = $this->staff->address;
        $this->email = $this->staff->email;
        $this->serial_nb = $this->staff->serial_nb;

        // Load contact information
        $this->contactInfos = $this->staff->contactDetail()
            ->get()
            ->map(function($contact) {
                return [
                    'type' => $contact->type,
                    'value' => $contact->value
                ];
            })
            ->toArray();

        if (empty($this->contactInfos)) {
            $this->addContactInfo();
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

    public function removeContactInfo($index)
    {
        unset($this->contactInfos[$index]);
        $this->contactInfos = array_values($this->contactInfos);
    }

    public function update()
    {
        $this->validate();
        $this->dispatch('scrollToElement');

        try {



            // Same for serial number
            if ($this->serial_nb === $this->staff->serial_nb) {
                $this->rules['serial_nb'] = 'nullable|string|max:255';
            } else {
                $this->rules['serial_nb'] = 'nullable|string|max:255|unique:users,serial_nb,' . $this->staff->id;
            }


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

            $this->staff->update($updateData);

            // Update contact information
            $this->staff->contactDetail()->delete(); // Remove existing contacts
            $validContactInfos = array_filter($this->contactInfos, function($info) {
                return !empty($info['type']) && !empty($info['value']);
            });

            foreach ($validContactInfos as $info) {
                $this->staff->contactDetail()->create([
                    'type' => $info['type'],
                    'value' => $info['value']
                ]);
            }

            DB::commit();

            session()->flash('success', 'Staff updated successfully.');
            return redirect()->route('staffs');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating staff: ' . $e->getMessage());
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
        return view('livewire.Staffs.staff-edit');
    }
}
