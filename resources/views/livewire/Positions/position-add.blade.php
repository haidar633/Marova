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
                <div class="card-header d-flex align-items-center justify-content-between">Add New Intimacy
                    <span class="float-right mt-3">
                        <span class="d-flex align-items-center">
                            <a class="btn btn-primary" href="{{ route('positions') }}">All Intimacy</a>
                        </span>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-group col-12">
                                    <label for="name">Name</label>
                                    <input wire:model="name" type="text"
                                           class="form-control border border-2 p-2" id="name"
                                           placeholder="Enter name">
                                </div>
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
                            <div class="text-danger">{{ $message }}</div> @enderror
                            @if ($photo && $newPhotoUploaded)
                                <img src="{{ $photo->temporaryUrl() }}" class="img-thumbnail mt-2" width="150"/>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-12 mt-4">
                        <label for="description">Description</label>
                        <textarea wire:model="description" class="form-control border border-2 p-2"
                                  id="description" placeholder="Enter description"></textarea>
                        @error('description')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
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