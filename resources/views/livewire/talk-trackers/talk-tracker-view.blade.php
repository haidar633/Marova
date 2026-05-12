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
                                <h6 class="text-white text-capitalize ps-3 mb-0">Talk Tracker</h6>
                                <div class="me-3">
                                    <a href="{{ route('add-talk-tracker') }}" class="btn btn-outline-white btn-sm mb-0">
                                        <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Add New Conversation
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-0 pb-2">
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
                                <select class="form-control" wire:model.live="connectionRatingFilter">
                                    <option value="">All Ratings</option>
                                    <option value="1">1 Star</option>
                                    <option value="2">2 Stars</option>
                                    <option value="3">3 Stars</option>
                                    <option value="4">4 Stars</option>
                                    <option value="5">5 Stars</option>
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
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Topic</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Connection Rating</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Resolution</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($talkTrackers as $talk)
                                    <tr>
                                        <td>
                                            <strong>{{ \Carbon\Carbon::parse($talk->conversation_date)->format('M d, Y') }}</strong>
                                            @if($talk->conversation_time)
                                                <br><small>{{ \Carbon\Carbon::parse($talk->conversation_time)->format('h:i A') }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $talk->topic }}</td>
                                        <td>
                                            @if($talk->connection_rating)
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($talk->connection_rating >= $i)
                                                            <i class="fas fa-star" style="font-size: 1.5rem; color: #facc15;"></i>
                                                        @elseif($talk->connection_rating >= $i - 0.5)
                                                            <i class="fas fa-star-half-alt" style="font-size: 1.5rem; color: #facc15;"></i>
                                                        @else
                                                            <i class="far fa-star" style="font-size: 1.5rem; color: #ccc;"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            @else
                                                <span class="text-muted">No rating</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($talk->resolution_summary)
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#resolutionModal{{ $talk->id }}">
                                                    View Resolution
                                                </button>
                                                <div class="modal fade" id="resolutionModal{{ $talk->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Resolution Summary</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p style="white-space: pre-wrap;">{{ $talk->resolution_summary }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">No resolution</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('show-talk-tracker', $talk->id) }}" class="btn btn-info btn-link btn-sm">
                                                    <i class="material-icons">visibility</i>
                                                </a>
                                                <a href="{{ route('edit-talk-tracker', $talk->id) }}" class="btn btn-success btn-link btn-sm">
                                                    <i class="material-icons">edit</i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-link btn-sm" onclick="confirmDelete({{ $talk->id }})">
                                                    <i class="material-icons">close</i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <p class="text-muted">No talk tracker entries found</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($talkTrackers->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $talkTrackers->links() }}
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
            if (confirm('Are you sure you want to delete this talk tracker entry?')) {
                Livewire.dispatch('destroy', [id]);
            }
        }
    </script>
@endpush
