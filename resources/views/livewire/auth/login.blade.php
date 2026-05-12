<div class="container my-auto mt-5">
    @php
        // For guests, use session or localStorage for storing their sidebar color
        $activeColor = session('guest_sidebar_color', 'primary');
    @endphp
    <div class="row signin-margin">
        <div class="col-lg-4 col-md-8 col-12 mx-auto">
            <div class="card z-index-0 fadeIn3 fadeInBottom">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-{{ $activeColor }} shadow-primary border-radius-lg py-3 pe-1">
                        <h4 class="text-white font-weight-bolder text-center mt-2 mb-0" style="color: #ffffff;">Sign in</h4>
                        <div class="row mt-3"></div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit='store'>
                        @if (Session::has('status'))
                            <div class="alert alert-success alert-dismissible text-white" role="alert">
                                <span class="text-sm">{{ Session::get('status') }}</span>
                                <button type="button" class="btn-close text-lg py-3 opacity-10"
                                        data-bs-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="input-group input-group-outline mt-3 @if(strlen($email ?? '') > 0) is-filled @endif">
                            <label class="form-label">Email</label>
                            <input wire:model.live='email' type="email" class="form-control">
                        </div>
                        @error('email')
                        <p class='text-danger inputerror'>{{ $message }} </p>
                        @enderror

                        <div class="input-group input-group-outline mt-3 @if(strlen($password ?? '') > 0) is-filled @endif">
                            <label class="form-label">Password</label>
                            <input wire:model.live="password" type="password" class="form-control">
                        </div>
                        @error('password')
                        <p class='text-danger inputerror'>{{ $message }} </p>
                        @enderror
                        <div class="form-check form-switch d-flex align-items-center my-3">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label mb-0 ms-2" for="rememberMe">Remember me</label>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn bg-gradient-{{ $activeColor }} w-100 my-4 mb-2">Sign in</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
    <script>
        document.addEventListener('livewire:load', function () {
            // Get the stored sidebar color from session or fallback to default
            const color = localStorage.getItem('guest_sidebar_color') || 'primary';

            // Dynamically update the background color of the sidebar and the button
            const sidebar = document.querySelector('.bg-gradient-primary');
            if (sidebar) {
                sidebar.classList.remove('bg-gradient-primary');
                sidebar.classList.add('bg-gradient-' + color);
            }

            const signInButton = document.querySelector('.btn.bg-gradient-primary');
            if (signInButton) {
                signInButton.classList.remove('bg-gradient-primary');
                signInButton.classList.add('bg-gradient-' + color);
            }
        });

        Livewire.on('sidebarColorChanged', color => {
            localStorage.setItem('guest_sidebar_color', color); // Store the color in localStorage
        });

    </script>
@endpush
