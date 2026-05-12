<?php

namespace App\Http\Livewire\UserManagement;

use App\Models\User;
use Livewire\Component;

class UserProfile extends Component
{
    public User $user;

    public $password;
    public $password_confirmation;

    protected function rules(){
        return [
            'user.fname' => 'required',
            'user.email' => 'required|email|unique:users,email,'.$this->user->id,
//            'user.phone' => 'required|max:10',
            'user.about' => 'required:max:150',
//            'user.location' => 'required',
            'password' => 'nullable',
            'password_confirmation' => 'nullable|same:password',
        ];
    }

    public function mount() {
        $this->user = auth()->user();
    }

    public function updated($propertyName){
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->validate();

        if (env('IS_DEMO') && $this->user->id == 1){
            // For demo user, only allow limited updates
            if(auth()->user()->email == $this->user->email){
                // Save user profile fields
                $this->user->save();

                // Update password if provided
                if(!empty($this->password)) {
                    $this->user->password = $this->password;
                    $this->user->save();
                }

                return back()->withStatus('Profile successfully updated.');
            }

            return back()->with('demo', "You are in a demo version, you can't change the admin email.");
        }

        // Regular users can update all fields
        $this->user->save();

        // Update password if provided
        if(!empty($this->password)) {
            $this->user->password = $this->password;
            $this->user->save();
        }

        return back()->withStatus('Profile successfully updated.');
    }

    public function render()
    {
        return view('livewire.User-Management.user-profile');
    }
}
