<div>
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
                <div class="card-header d-flex align-items-center justify-content-between">Create Staff
                    <span class="float-right mt-3">
                        <span class="d-flex align-items-center">
                            <a class="btn btn-primary" href="{{ route('staffs') }}">Staffs</a>
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
                            <div class="text-danger">The email number field is required.</div>
                            @enderror
                        </div>


                    </div>
                    <!-- Password Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input wire:model.blur="password" type="password"
                                       class="form-control border border-2 p-2"
                                       placeholder="Enter password">
                                @error('password')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Repeat Password</label>
                                <input wire:model.blur="password_confirmation" type="password"
                                       class="form-control border border-2 p-2"
                                       placeholder="Re-enter your password">
                                @error('password_confirmation')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
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
