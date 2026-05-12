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
                                <div class="d-flex align-items-center">
                                    <h6 class="text-white text-capitalize ps-3 mb-0">All Partners</h6>
                                    <div class="ms-4 d-inline-flex align-items-center px-3 py-2 rounded-pill shadow-sm" style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.5); backdrop-filter: blur(10px); box-shadow: 0 0 15px rgba(255, 255, 255, 0.3), inset 0 0 10px rgba(255, 255, 255, 0.1);">
                                        <i class="material-icons text-white me-2" style="font-size: 20px; text-shadow: 0 0 10px rgba(255,255,255,0.5);">people</i>
                                        <span class="text-white fw-bolder" style="font-size: 1.1rem; line-height: 1; text-shadow: 0 0 10px rgba(255,255,255,0.5);">{{ $partners->count() }}</span>
                                        <span class="text-white text-uppercase ms-2" style="font-size: 0.7rem; opacity: 0.9; font-weight: 800; letter-spacing: 1px; text-shadow: 0 0 5px rgba(255,255,255,0.3);">Total</span>
                                    </div>
                                </div>
                                <div class="me-3 d-flex gap-2">
                                    <button wire:click="toggleShowFavorites" class="btn btn-sm mb-0 {{ $showFavorites ? 'btn-white text-primary' : 'btn-outline-white' }}">
                                        <i class="material-icons text-sm">{{ $showFavorites ? 'favorite' : 'favorite_border' }}</i>&nbsp;&nbsp;View Favorites
                                    </button>
                                    <a href="{{ route('add-partner') }}" class="btn btn-outline-white btn-sm mb-0">
                                        <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Add New Partner
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
                                    <label class="form-label">Search partners...</label>
                                    <input type="text" class="form-control" wire:model.live="search">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive p-0">
                            <table class="table align-middle text-center">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Image</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Star Rating</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Last Met</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Description</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($partners as $partner)
                                    <tr>
                                        

                                        <td class="text-center align-middle">
                                            @if($partner->image)
                                                <div class="mx-auto shadow-md border-radius-lg overflow-hidden" 
                                                     style="width: 260px; height: 340px; border: 4px solid #fff;">
                                                    <img src="{{ asset('storage/' . $partner->image) }}"
                                                         style="width: 100%; height: 100%; object-fit: cover; object-position: center;"
                                                         alt="Partner Image">
                                                </div>
                                            @else
                                                <div class="avatar avatar-xl bg-gradient-secondary d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 260px; height: 340px;">
                                                    <i class="material-icons text-white" style="font-size: 60px;">image</i>
                                                </div>
                                            @endif
                                        </td>

                                        <td class="text-center align-middle">
                                            <h6 class="mb-0 text-lg text-secondary">{{ $partner->name ?? 'N/A' }}</h6>
                                        </td>


                                        <td class="text-center align-middle">
                                            @if($partner->star_rating)
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($partner->star_rating >= $i)
                                                            <i class="fas fa-star" style="font-size: 3rem; color: #facc15;"></i>
                                                        @elseif($partner->star_rating >= $i - 0.5)
                                                            <i class="fas fa-star-half-alt" style="font-size: 3rem; color: #facc15;"></i>
                                                        @else
                                                            <i class="far fa-star" style="font-size: 3rem; color: #ccc;"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            @else
                                                <span class="text-muted">No rating</span>
                                            @endif
                                        </td>

                                        <td class="text-center align-middle">
                                            @if($partner->last_day_we_met)
                                                <div class="mb-2">
                                                    <strong>{{ \Carbon\Carbon::parse($partner->last_day_we_met)->format('M d, Y') }}</strong>
                                                </div>
                                                <div class="text-primary fw-bold" style="font-size: 1.3rem; letter-spacing: 0.5px;">{{ $partner->time_passed }}</div>
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </td>

                                        <td class="text-center align-middle">
                                            @if($partner->description)
                                                <button type="button" 
                                                        class="btn btn-info btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#descriptionModal{{ $partner->id }}"
                                                        title="View Description">
                                                    <i class="material-icons">description</i>
                                                    View Description
                                                </button>
                                                
                                                <!-- Modal for full description -->
                                                <div class="modal fade" id="descriptionModal{{ $partner->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                                                        <div class="modal-content" style="border-radius: 15px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                                                            <div class="modal-header bg-gradient-primary text-white" style="padding: 1.5rem 2rem; border-bottom: none;">
                                                                <h5 class="modal-title d-flex align-items-center mb-0 text-white">
                                                                    <i class="material-icons me-2" style="font-size: 28px; color: white;">description</i>
                                                                    <div>
                                                                        <div style="font-size: 1.25rem; font-weight: 600; color: white;">{{ $partner->name ?? 'Partner' }}</div>
                                                                        <div style="font-size: 0.875rem; opacity: 0.9; font-weight: 400; color: white;">Description</div>
                                                                    </div>
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.8;"></button>
                                                            </div>
                                                            <div class="modal-body" style="padding: 2.5rem; background: #f8f9fa; overscroll-behavior: contain;">
                                                                <div style="white-space: pre-wrap; word-wrap: break-word; line-height: 1.8; color: #344767; font-size: 1.1rem; font-family: 'Roboto', sans-serif; text-align: center; margin: 0; background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid #e9ecef;">{{ $partner->description }}</div>
                                                            </div>
                                                            <div class="modal-footer" style="padding: 1rem 2rem; border-top: 1px solid #e9ecef; background: white;">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 0.5rem 1.5rem; border-radius: 8px;">
                                                                    <i class="material-icons me-1" style="font-size: 18px; vertical-align: middle;">close</i>
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">
                                                    <i class="material-icons" style="font-size: 18px; vertical-align: middle;">info_outline</i>
                                                    No description
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-primary btn-link"
                                                        wire:click="openVisitModal({{ $partner->id }})"
                                                        title="Log Visit">
                                                     <i class="material-icons">event</i>
                                                     <div class="ripple-container"></div>
                                                 </button>

                                                 <a href="{{ route('edit-partner', $partner->id) }}" class="btn btn-success btn-link" data-original-title="" title="">
                                                     <i class="material-icons">edit</i>
                                                     <div class="ripple-container"></div>
                                                 </a>

                                                 <button type="button" wire:click="toggleFavorite({{ $partner->id }})" 
                                                         class="btn btn-link {{ $partner->is_favorite ? 'text-danger' : 'text-secondary' }}" 
                                                         title="{{ $partner->is_favorite ? 'Remove from favorites' : 'Add to favorites' }}">
                                                     <i class="material-icons">{{ $partner->is_favorite ? 'favorite' : 'favorite_border' }}</i>
                                                     <div class="ripple-container"></div>
                                                 </button>

                                                <button type="button" class="btn btn-danger btn-link"
                                                        data-original-title="" title=""
                                                        onclick="confirmDelete({{ $partner->id }})">
                                                    <i class="material-icons">close</i>
                                                    <div class="ripple-container"></div>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="material-icons text-muted mb-2" style="font-size: 48px;">people</i>
                                                <p class="text-muted mb-0">No partners found</p>
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

                        @if($partners->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $partners->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Visit Modal -->
    <div class="modal fade" id="logVisitModal" tabindex="-1" aria-labelledby="logVisitModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title text-white" id="logVisitModalLabel">Log Visit</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="logVisit">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Visit Date</label>
                            <input type="date" class="form-control border border-2 p-2" wire:model="visitDate">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Notes (Optional)</label>
                            <textarea class="form-control border border-2 p-2" rows="3" wire:model="visitNotes" placeholder="How was the visit?"></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Visit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this partner?')) {
                Livewire.dispatch('destroy', [id]);
            }
        }

        document.addEventListener('livewire:init', () => {
            const visitModal = new bootstrap.Modal(document.getElementById('logVisitModal'));

            Livewire.on('open-visit-modal', () => {
                visitModal.show();
            });

            Livewire.on('close-visit-modal', () => {
                visitModal.hide();
            });
        });
    </script>
@endpush
