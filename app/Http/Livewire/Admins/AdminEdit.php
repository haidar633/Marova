<?php

namespace App\Http\Livewire\Admins;

use App\Models\User;
use Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class AdminEdit extends Component
{
    use AuthorizesRequests;

    public $adminId;
    public $selectedrole;
    public $fname;
    public $lname;
    public $date_of_birth;
    public $address;
    public $password;
    public $email;
    public $password_confirmation;
    public $serial_nb;
    public $contactInfos = [];


    private $originalFname;
    private $originalLname;

    protected $listeners = [
        'cardLoaded',
    ];

    protected $rules = [
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'date_of_birth' => 'required|date',
        'address' => 'required|string|max:255',
        'email' => 'required|email',
        'password' => 'nullable',
        'password_confirmation' => 'nullable|same:password',
        'serial_nb' => 'nullable|string|max:255',
        'contactInfos.*.type' => 'required|string|in:phone,whatsapp',
        'contactInfos.*.value' => 'required|string',
    ];

    public function mount($id)
    {
        $this->authorize('user-edit');
        $this->adminId = $id; // Fix: Set the adminId property
        $this->roles = Role::whereNotIn('id', [1])->pluck('name', 'id')->all();

        $user = User::with(['contactDetail', 'roles'])->findOrFail($id);

        $this->fname = $user->fname;
        $this->lname = $user->lname;
        $this->email = $user->email;
        $this->date_of_birth = $user->date_of_birth;
        $this->address = $user->address;
        $this->serial_nb = $user->serial_nb;

        // Load user role
        if ($user->roles->first()) {
            $this->selectedrole = $user->roles->first()->id;
        }

        // Load contact information
        if ($user->contactDetail->count() > 0) {
            foreach ($user->contactDetail as $contact) {
                $this->contactInfos[] = [
                    'id' => $contact->id,
                    'type' => $contact->type,
                    'value' => $contact->value
                ];
            }
        }

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
        $contactInfo = $this->contactInfos[$index];

        // Delete from database if it exists
        if (!empty($contactInfo['id'])) {
            try {
                $user = User::find($this->adminId);
                $user->contactDetail()->where('id', $contactInfo['id'])->delete();
            } catch (\Exception $e) {
                \Log::error('Error deleting contact info', [
                    'error' => $e->getMessage(),
                    'contact_id' => $contactInfo['id']
                ]);
            }
        }

        unset($this->contactInfos[$index]);
        $this->contactInfos = array_values($this->contactInfos);
    }

    public function update()
    {
        $this->validate();

        try {

            $user = User::findOrFail($this->adminId);

            $userData = [
                'fname' => $this->fname,
                'lname' => $this->lname,
                'email' => $this->email,
                'date_of_birth' => $this->date_of_birth,
                'address' => $this->address,
                'serial_nb' => $this->serial_nb,
            ];

            if (!empty($this->password)) {
                $userData['password'] = $this->password;
            }
            $user->update($userData);


            // Filter and update contact info
            $validContactInfos = array_filter($this->contactInfos, function ($info) {
                return !empty($info['type']) && !empty($info['value']);
            });

            foreach ($validContactInfos as $info) {
                if (!empty($info['id'])) {
                    // Update existing contact detail
                    $user->contactDetail()->where('id', $info['id'])->update([
                        'type' => $info['type'],
                        'value' => $info['value']
                    ]);
                } else {
                    // Create new contact detail
                    $user->contactDetail()->create([
                        'type' => $info['type'],
                        'value' => $info['value']
                    ]);
                }
            }

            session()->flash('message', 'Admin updated successfully.');
            return redirect()->route('admins');

        } catch (\Exception $e) {
            \Log::error('Error updating admin', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $this->adminId
            ]);

            session()->flash('error', 'Error updating admin: ' . $e->getMessage());
            $this->dispatch('showAlert', [
                'type' => 'error',
                'message' => 'Error updating admin: ' . $e->getMessage()
            ]);
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
        return view('livewire.Admins.admin-edit');
    }
}
