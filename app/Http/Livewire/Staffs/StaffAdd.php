<?php

namespace App\Http\Livewire\Staffs;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class StaffAdd extends Component
{
    public $fname;
    public $lname;
    public $date_of_birth;
    public $address;
    public $password;
    public $email;
    public $password_confirmation;


    protected $rules = [
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'date_of_birth' => 'required|date',
        'address' => 'required|string|max:255',
        'email' => 'required|string|email|unique:users,email',
        'password' => 'required',
        'password_confirmation' => 'required|same:password',
    ];


    public function mount()
    {
        $this->authorize('staff-create');
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

    public function store()
    {

        $this->dispatch('scrollToElement');
        $this->validate();
        try {

            DB::beginTransaction();

            // Create staff
            $staff = User::create([
                'fname' => $this->fname,
                'lname' => $this->lname,
                'email' => $this->email,
                'type' => 'Staff',
                'date_of_birth' => $this->date_of_birth,
                'address' => $this->address,
                'password' => $this->password,
            ]);


            $staffRole = Role::where('name', 'Staff')->firstOrFail();
            $staff->assignRole($staffRole);

            // Save contact info
            $validContactInfos = array_filter($this->contactInfos, function ($info) {
                return !empty($info['type']) && !empty($info['value']);
            });

            foreach ($validContactInfos as $info) {
                $staff->contactDetail()->create([
                    'type' => $info['type'],
                    'value' => $info['value']
                ]);
            }


            DB::commit();

            session()->flash('success', 'Staff created successfully.');
            return redirect()->route('staffs');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating staff: ' . $e->getMessage());
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

    public function render()
    {
        return view('livewire.Staffs.staff-add');
    }
}
