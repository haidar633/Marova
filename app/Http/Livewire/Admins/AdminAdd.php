<?php

namespace App\Http\Livewire\Admins;

use App\Models\User;
use Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class AdminAdd extends Component
{
    use AuthorizesRequests;

    public $notBooted = true;


    public $fname;
    public $lname;
    public $date_of_birth;
    public $address;
    public $password;
    public $email;
    public $password_confirmation;
    public $serial_nb;

    public $contactInfos = [];

    protected $listeners = [
        'cardLoaded',
    ];

    protected $rules = [
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'date_of_birth' => 'required|date',
        'address' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required',
        'password_confirmation' => 'required|same:password',
        'serial_nb' => 'nullable|string|max:255',
        'contactInfos.*.type' => 'required|string|in:phone,whatsapp',
        'contactInfos.*.value' => 'required|string',
    ];

    public function mount($roleName = null)
    {
        $this->authorize('admin-create');
        $this->addContactInfo();
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
        $this->validate();
        $this->dispatch('scrollToElement');

        $validContactInfos = array_filter($this->contactInfos, function ($info) {
            return !empty($info['type']) && !empty($info['value']);
        });

        try {
            // Create user
            $user = User::create([
                'fname' => $this->fname,
                'lname' => $this->lname,
                'type' => "Admin",
                'email' => $this->email,
                'date_of_birth' => $this->date_of_birth,
                'address' => $this->address,
                'serial_nb' => $this->serial_nb,
                'password' => $this->password,
            ]);

            $adminRole = Role::where('name', 'Admin')->firstOrFail();
            $user->assignRole($adminRole);

            // Save contact info
            foreach ($validContactInfos as $info) {
                $user->contactDetail()->create([
                    'type' => $info['type'],
                    'value' => $info['value']
                ]);
            }

            session()->flash('message', 'Admin created successfully.');
            return redirect()->route('admins');

        } catch (\Exception $e) {
            \Log::error('Error creating admin or contact details', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_data' => $this->fname . ' ' . $this->lname,
                'contact_infos' => $validContactInfos
            ]);

            session()->flash('error', 'Error creating admin: ' . $e->getMessage());
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
        return view('livewire.Admins.admin-add');
    }
}
