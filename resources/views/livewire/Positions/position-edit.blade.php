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
                        Oops!
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
                <div class="card-header d-flex align-items-center justify-content-between">
                    Edit Intimacy
                    <span class="float-right">
                        <span class="d-flex align-items-center">
                            <a class="btn btn-primary" href="{{ route('positions') }}">Back to Intimacy</a>
                        </span>
                    </span>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input wire:model.live="name" type="text"
                                       class="form-control border border-2 p-2" id="name"
                                       placeholder="Enter intimacy name">
                                @error('name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="photo">Photo</label>
                            <input type="file" id="photo" class="form-control border border-2 p-2"
                                   wire:model="photo">
                            @error('photo')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror

                            <!-- Show new uploaded photo preview -->
                            @if ($photo && $newPhotoUploaded)
                                <div class="mt-2">
                                    <img src="{{ $photo->temporaryUrl() }}" class="img-thumbnail" width="150"/>
                                </div>
                            @endif

                            <!-- Show existing photo -->
                            @if ($existingPhoto && !$newPhotoUploaded)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $existingPhoto) }}" class="img-thumbnail" width="150">
                                </div>
                            @endif

                            <!-- Show no photo message -->
                            @if (!$existingPhoto && !$newPhotoUploaded)
                                <p class="text-sm text-muted mt-2">No photo uploaded</p>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" wire:click="update" wire:loading.attr="disabled" wire:target="update"
                                class="btn bg-gradient-dark btn-md mt-4 mb-4">
                            <span wire:loading.remove wire:target="update">Update Intimacy</span>
                            <span wire:loading wire:target="update">Updating...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
