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
                                <h6 class="text-white text-capitalize ps-3 mb-0">Mood Journal</h6>
                                <div class="me-3">
                                    <a href="{{ route('add-mood-journal') }}" class="btn btn-outline-white btn-sm mb-0">
                                        <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Add New Entry
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-0 pb-2">
                        @if($activeVibeCheck)
                            <div class="alert alert-info mx-4 mt-3 mb-3">
                                <strong>Current Vibe:</strong> {{ $activeVibeCheck->status }}
                                @if($activeVibeCheck->context)
                                    - {{ $activeVibeCheck->context }}
                                @endif
                            </div>
                        @endif

                        <div class="row px-4 mb-3">
                            <div class="col-md-3">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Search...</label>
                                    <input type="text" class="form-control" wire:model.live="search">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">From Date</label>
                                    <input type="date" class="form-control" wire:model.live="dateFrom">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">To Date</label>
                                    <input type="date" class="form-control" wire:model.live="dateTo">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" wire:model.live="emotionalStateFilter">
                                    <option value="">All States</option>
                                    <option value="Secure">Secure</option>
                                    <option value="Anxious/Toxic">Anxious/Toxic</option>
                                    <option value="Overwhelmed">Overwhelmed</option>
                                    <option value="Happy">Happy</option>
                                    <option value="Sad">Sad</option>
                                    <option value="Angry">Angry</option>
                                    <option value="Calm">Calm</option>
                                    <option value="Excited">Excited</option>
                                    <option value="Stressed">Stressed</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-12 mt-2">
                                <button class="btn btn-sm btn-secondary" wire:click="clearFilters">Clear Filters</button>
                            </div>
                        </div>

                        <div class="table-responsive p-0">
                            <table class="table align-middle text-center">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Emotional State</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">POV</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Discussed</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($moodJournals as $journal)
                                    <tr>
                                        <td>
                                            <strong>{{ \Carbon\Carbon::parse($journal->entry_date)->format('M d, Y') }}</strong>
                                            @if($journal->entry_time)
                                                <br><small>{{ \Carbon\Carbon::parse($journal->entry_time)->format('h:i A') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-gradient-{{ $journal->emotional_state == 'Secure' ? 'success' : ($journal->emotional_state == 'Anxious/Toxic' ? 'danger' : 'warning') }}">
                                                {{ $journal->emotional_state }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($journal->pov)
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#povModal{{ $journal->id }}">
                                                    View POV
                                                </button>
                                                <div class="modal fade" id="povModal{{ $journal->id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                        <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
                                                            <div class="modal-header bg-gradient-info text-white">
                                                                <h5 class="modal-title text-white">Point of View</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body" style="background: #f8f9fa; padding: 1.5rem; overscroll-behavior: contain;">
                                                                <div style="white-space: pre-wrap; word-wrap: break-word; line-height: 1.7; color: #344767; font-size: 1rem; background: white; padding: 1rem; border-radius: 8px; border: 1px solid #e9ecef; text-align: center;">{{ $journal->pov }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">No POV</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($journal->discussed_with_partner)
                                                <i class="material-icons text-success">check_circle</i>
                                            @else
                                                <i class="material-icons text-muted">cancel</i>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('show-mood-journal', $journal->id) }}" class="btn btn-info btn-link btn-sm">
                                                    <i class="material-icons">visibility</i>
                                                </a>
                                                <a href="{{ route('edit-mood-journal', $journal->id) }}" class="btn btn-success btn-link btn-sm">
                                                    <i class="material-icons">edit</i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-link btn-sm" onclick="confirmDelete({{ $journal->id }})">
                                                    <i class="material-icons">close</i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <p class="text-muted">No mood journal entries found</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($moodJournals->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $moodJournals->links() }}
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
            if (confirm('Are you sure you want to delete this mood journal entry?')) {
                Livewire.dispatch('destroy', [id]);
            }
        }
    </script>
@endpush
