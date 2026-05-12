<div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('success') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('error') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif


    <div class="container">
        <div class="justify-content-center">
            @if (count($errors) > 0)
                <div class="alert alert-danger alert-dismissible text-white" role="alert">
                    <span class="text-lg">
                        Opps!
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </span>
                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="overlay">
                <div class="loader"></div>
            </div>
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">Create Patient
                    <span class="float-right mt-3">
                        <span class="d-flex align-items-center">
                            <a class="btn btn-primary" href="{{ route('patients') }}">Patients</a>
                        </span>
                    </span>
                </div>
                <div class="card-body">
                    <!-- Basic Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-group col-12">
                                    <label for="fname">First Name</label>
                                    <input wire:model.live="fname" type="text"
                                           class="form-control border border-2 p-2" id="fname"
                                           placeholder="Enter first name">
                                </div>
                                @error('fname')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-group col-12">
                                    <label for="lname">Last Name</label>
                                    <input wire:model.live="lname" type="text"
                                           class="form-control border border-2 p-2" id="lname"
                                           placeholder="Enter last name">
                                </div>
                                @error('lname')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-group col-12">
                                    <label for="date_of_birth">Date Of Birth</label>
                                    <input wire:model.live="date_of_birth" type="date"
                                           class="form-control border border-2 p-2" id="date_of_birth">
                                </div>
                                @error('date_of_birth')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-group col-12">
                                    <label for="address">Address</label>
                                    <input wire:model.live="address" type="text"
                                           class="form-control border border-2 p-2" id="address"
                                           placeholder="Enter address">
                                </div>
                                @error('address')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-6 mt-3">
                            <label for="email">Email</label>
                            <input wire:model="email" type="email"
                                   class="form-control border border-2 p-2" id="email">
                            @error('email')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{--                        @if($fname || $lname)--}}
                        <div class="form-group col-6 mt-3">
                            <label for="serial_nb">Serial Number</label>
                            <input wire:model.live="serial_nb" type="text"
                                   class="form-control border border-2 p-2" id="serial_nb" readonly>
                            @error('serial_nb')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        {{--                        @endif--}}


                    </div>


                    <!-- Contact Information Section -->
                    <div class="mt-4">
                        <h2>Contact Info</h2>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <button class="btn btn-success w-100" wire:click.prevent="addContactInfo">
                                    Add Contact Info
                                </button>
                            </div>
                        </div>
                        @foreach($contactInfos as $index => $info)
                            <div class="row mt-3 align-items-end">
                                <div class="col-md-3">
                                    <select wire:model.live="contactInfos.{{ $index }}.type"
                                            class="form-select border border-2 p-2">
                                        <option value="">Select Type</option>
                                        <option value="phone">Phone</option>
                                        <option value="whatsapp">WhatsApp</option>
                                    </select>
                                    @error("contactInfos.{$index}.type")
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if($info['type'])
                                    <div class="col-md-7">
                                        <div class="form-group mb-0">
                                            <label class="mb-2">
                                                @if($info['type'] === 'phone')
                                                    Phone {{ $this->getPhoneLabel($index) }}
                                                @else
                                                    {{ ucfirst($info['type']) }}
                                                @endif
                                            </label>
                                            <input wire:model.live="contactInfos.{{ $index }}.value"
                                                   type="{{ $info['type'] === 'email' ? 'email' : 'text' }}"
                                                   class="form-control border border-2 p-2"
                                                   placeholder="Enter {{ $info['type'] }}">
                                        </div>
                                        @error("contactInfos.{$index}.value")
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                @endif

                                <div class="col-md-2">
                                    @if($index > 0)
                                        <button class="btn btn-danger w-100 mb-1"
                                                wire:click.prevent="removeContactInfo({{ $index }})">
                                            Remove
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach


                    </div>



                    <div class="row mt-6">
                        <h2>Medical History</h2>
                        <div class="col-md-6">
                            <label for="medications">Medications & Dosage</label>
                            <textarea wire:model="medications" class="form-control border border-2 p-2" rows="2"></textarea>
                            @error('medications') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="allergies">Allergies</label>
                            <textarea wire:model="allergies" class="form-control border border-2 p-2" rows="2"></textarea>
                            @error('allergies') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>


                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="chronic_disease">Chronic Disease</label>
                            <textarea wire:model="chronic_disease" class="form-control border border-2 p-2" rows="2"></textarea>
                            @error('chronic_disease') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>


                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label for="height">Height (cm)</label>
                            <input wire:model="height" type="number" class="form-control border border-2 p-2" step="0.1">
                            @error('height') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="weight">Weight (kg)</label>
                            <input wire:model="weight" type="number" class="form-control border border-2 p-2" step="0.1">
                            @error('weight') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>


                        <div class="col-md-3">
                            <label for="referral">Referral</label>
                            <input wire:model="referral" type="text" class="form-control border border-2 p-2">
                            @error('referral') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>


                    <div class="d-flex justify-content-end">
                        <button type="submit" wire:click="store" wire:loading.attr="disabled"
                                class="btn bg-gradient-dark btn-md mt-4 mb-4">
                            {{ 'Save' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
