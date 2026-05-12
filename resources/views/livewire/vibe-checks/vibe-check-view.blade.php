<div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('success') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Vibe Check</h6>
                                <div class="me-3">
                                    <a href="{{ route('add-vibe-check') }}" class="btn btn-outline-white btn-sm mb-0">
                                        <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Add New Status
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-0 pb-2">
                        @if($activeVibeCheck)
                            <div class="alert alert-{{ $activeVibeCheck->status == 'Available for Connection' ? 'success' : 'warning' }} mx-4 mt-3 mb-3">
                                <h5><strong>Current Status:</strong> {{ $activeVibeCheck->status }}</h5>
                                @if($activeVibeCheck->context)
                                    <p class="mb-0">{{ $activeVibeCheck->context }}</p>
                                @endif
                                <small>Last updated: {{ $activeVibeCheck->updated_at->format('M d, Y h:i A') }}</small>
                            </div>
                        @endif

                        <div class="row px-4 mb-3">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Search...</label>
                                    <input type="text" class="form-control" wire:model.live="search">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive p-0">
                            <table class="table align-middle text-center">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Context</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Active</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Updated</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($vibeChecks as $vibe)
                                    <tr>
                                        <td>
                                            <span class="badge bg-gradient-{{ $vibe->status == 'Available for Connection' ? 'success' : 'warning' }}">
                                                {{ $vibe->status }}
                                            </span>
                                        </td>
                                        <td>{{ $vibe->context ?? 'No context' }}</td>
                                        <td>
                                            @if($vibe->is_active)
                                                <i class="material-icons text-success">check_circle</i>
                                            @else
                                                <i class="material-icons text-muted">cancel</i>
                                            @endif
                                        </td>
                                        <td>{{ $vibe->updated_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button wire:click="toggleStatus({{ $vibe->id }})" class="btn btn-{{ $vibe->is_active ? 'warning' : 'success' }} btn-link btn-sm">
                                                    <i class="material-icons">{{ $vibe->is_active ? 'pause' : 'play_arrow' }}</i>
                                                </button>
                                                <a href="{{ route('edit-vibe-check', $vibe->id) }}" class="btn btn-info btn-link btn-sm">
                                                    <i class="material-icons">edit</i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-link btn-sm" onclick="confirmDelete({{ $vibe->id }})">
                                                    <i class="material-icons">close</i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <p class="text-muted">No vibe checks found</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($vibeChecks->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $vibeChecks->links() }}
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
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this vibe check?')) {
                Livewire.dispatch('destroy', [id]);
            }
        }
    </script>
@endpush
