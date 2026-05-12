<?php

namespace App\Http\Livewire\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{

    public $email='';
    public $password='';

    protected $rules= [
        'email' => 'required|email',
        'password' => 'required'

    ];

    public function render()
    {
        return view('livewire.auth.login');
    }


    public function store()
    {
        $attributes = $this->validate();

        if (!auth()->attempt($attributes)) {
            throw ValidationException::withMessages([
                'email' => 'Your provided credentials could not be verified.'
            ]);
        }

        $user = auth()->user();

        // Check if doctor is not active
        if ($user->type === 'Doctor') {
            $details = DB::table('user_details')->where('user_id', $user->id)->first();

            if (!$details || !$details->dr_is_active) {
                auth()->logout(); // Logout immediately
                throw ValidationException::withMessages([
                    'email' => 'Your account is not active. Please contact admin.'
                ]);
            }

            session()->regenerate();
            return redirect('/appointments');
        }

        session()->regenerate();
        return redirect('/attempts');
    }

}
