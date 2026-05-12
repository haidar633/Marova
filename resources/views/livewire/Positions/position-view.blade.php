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

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">All Intimacy</h6>
                                <div class="me-3">
                                    <button wire:click="goToFavorites" class="btn btn-outline-white btn-sm mb-0 me-2">
                                        <i class="material-icons text-sm">favorite</i>&nbsp;&nbsp;View Favorites
                                    </button>
                                    <a href="{{ route('add-position') }}" class="btn btn-outline-white btn-sm mb-0">
                                        <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Add New Intimacy
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-0 pb-2">
                        <!-- Search Bar -->
                        <div class="row px-4 mb-3">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Search intimacy...</label>
                                    <input type="text" class="form-control" wire:model.live="search">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive p-0">
                            <table class="table align-middle text-center">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Photo</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Favorite</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($positions as $position)
                                    <tr>
                                        <td class="text-center align-middle">
                                            @if($position->photo)
                                                <div class="mx-auto shadow-sm border-radius-lg overflow-hidden" 
                                                     style="width: 160px; height: 120px; border: 2px solid #fff;">
                                                    <img src="{{ asset('storage/' . $position->photo) }}"
                                                         style="width: 100%; height: 100%; object-fit: cover; object-position: center;"
                                                         alt="Intimacy Photo">
                                                </div>
                                            @else
                                                <div class="avatar avatar-lg bg-gradient-secondary d-flex align-items-center justify-content-center mx-auto shadow-sm">
                                                    <i class="material-icons text-white">image</i>
                                                </div>
                                            @endif
                                        </td>

                                        <td class="text-center align-middle">
                                            <h6 class="mb-0 text-lg text-secondary">{{ $position->name }}</h6>
                                        </td>

                                        <td class="text-center align-middle">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="favSwitch-{{ $position->id }}"
                                                    wire:change="toggleFavorite({{ $position->id }})"
                                                    @if($position->is_favorite) checked @endif
                                                >
                                            </div>
                                        </td>

                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center gap-2">
                                                                                               <a rel="tooltip" class="btn btn-success btn-link"
                                                   href="{{ route('edit-position', $position->id) }}" data-original-title=""
                                                   title="">
                                                    <i class="material-icons">edit</i>
                                                    <div class="ripple-container"></div>
                                                </a>

                                                <button type="button" class="btn btn-danger btn-link"
                                                        data-original-title="" title=""
                                                        onclick="confirmDelete({{ $position->id }})">
                                                    <i class="material-icons">close</i>
                                                    <div class="ripple-container"></div>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="material-icons text-muted mb-2" style="font-size: 48px;">inventory_2</i>
                                                <p class="text-muted mb-0">No intimacy records found</p>
                                                @if(!empty($search))
                                                    <p class="text-sm text-muted">Try adjusting your search criteria</p>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($positions->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $positions->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
