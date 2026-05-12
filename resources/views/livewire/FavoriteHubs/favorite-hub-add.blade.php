<div>
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

            <div class="overlay" wire:loading>
                <div class="loader"></div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Create Favorate Hub</h5>
                    <a class="btn btn-primary" href="{{ route('favorite-hubs') }}">Favorite Hubs</a>
                </div>

                <div class="card-body">
                    <form wire:submit.prevent="store">
                        <div class="row">


                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="link">Link<span class="text-danger"> *</span></label>
                                    <input wire:model="link" type="text" class="form-control border border-2 p-2" id="link" placeholder="Enter link">
                                    @error('link')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="description">Description<span class="text-danger"> *</span></label>
                                    <input wire:model="description" type="text" class="form-control border border-2 p-2" id="description" placeholder="Enter description">
                                    @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


