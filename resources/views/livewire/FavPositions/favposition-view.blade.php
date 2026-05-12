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
                        <div class="bg-gradient-warning shadow-warning border-radius-lg pt-4 pb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">
                                    <i class="material-icons me-2">favorite</i>Favorite Intimacy
                                </h6>
                                <div class="me-3">
                                    <a href="{{ route('positions') }}" class="btn btn-outline-white btn-sm mb-0">
                                        <i class="material-icons text-sm">arrow_back</i>&nbsp;&nbsp;Back to All
                                        Intimacy
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
                                    <label class="form-label">Search favorite intimacy...</label>
                                    <input type="text" class="form-control" wire:model.live="search">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0 text-center">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Photo</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Description</th>
                                    <th class="text-secondary opacity-7">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($favPositions as $position)
                                    <tr>
                                        <td class="align-middle">
                                            @if($position->photo)
                                                <div class="mx-auto shadow-sm border-radius-lg overflow-hidden" 
                                                     style="width: 160px; height: 120px; border: 2px solid #fff;">
                                                    <img src="{{ asset('storage/' . $position->photo) }}"
                                                         style="width: 100%; height: 100%; object-fit: cover; object-position: center;"
                                                         alt="Intimacy Photo">
                                                </div>
                                            @else
                                                <div class="avatar avatar-lg bg-gradient-warning d-flex align-items-center justify-content-center mx-auto shadow-sm">
                                                    <i class="material-icons text-white">favorite</i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <h6 class="mb-0 text-sm">{{ $position->name }}</h6>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs text-secondary mb-0">
                                                {{ $position->description ? Str::limit($position->description, 50) : 'No description' }}
                                            </p>
                                        </td>
                                        <td class="align-middle">
                                            <button onclick="confirmUnfavorite({{ $position->id }})"
                                                    class="btn btn-link text-warning text-gradient px-2 mb-0"
                                                    title="Remove from favorites">
                                                <i class="material-icons" style="font-size: 24px">favorite</i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="material-icons text-muted mb-2" style="font-size: 48px;">favorite_border</i>
                                                <p class="text-muted mb-2">No favorite intimacy records found</p>
                                                @if(!empty($search))
                                                    <p class="text-sm text-muted mb-2">Try adjusting your search criteria</p>
                                                @else
                                                    <p class="text-sm text-muted mb-2">Start adding intimacy items to your favorites!</p>
                                                @endif
                                                <a href="{{ route('positions') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="material-icons text-sm me-1">add</i>Browse Intimacy
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($favPositions->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $favPositions->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        function confirmUnfavorite(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This intimacy item will be removed from favorites.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, remove it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Use the new Livewire 3 syntax
                @this.call('removeFavorite', id);
                }
            });
        }
    </script>
@endpush
