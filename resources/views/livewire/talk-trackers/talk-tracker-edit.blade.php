<div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('success') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="container">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>Edit Talk Tracker Entry</span>
                <a class="btn btn-primary" href="{{ route('talk-trackers') }}">Back</a>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="update">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Conversation Date</label>
                                <input type="date" wire:model="conversation_date" class="form-control border border-2 p-2">
                                @error('conversation_date') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Conversation Time</label>
                                <input type="time" wire:model="conversation_time" class="form-control border border-2 p-2">
                                @error('conversation_time') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Topic</label>
                                <input type="text" wire:model="topic" class="form-control border border-2 p-2">
                                @error('topic') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Connection Rating (1-5 stars)</label>
                                <input type="number" wire:model="connection_rating" min="1" max="5" step="0.5" class="form-control border border-2 p-2">
                                @error('connection_rating') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Link to Mood Journal</label>
                                <select wire:model="mood_journal_id" class="form-control border border-2 p-2">
                                    <option value="">None</option>
                                    @foreach($moodJournals as $journal)
                                        <option value="{{ $journal->id }}">{{ $journal->entry_date->format('M d, Y') }} - {{ $journal->emotional_state }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Resolution Summary</label>
                                <textarea wire:model="resolution_summary" class="form-control border border-2 p-2" rows="5"></textarea>
                                @error('resolution_summary') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn bg-gradient-dark btn-md">Update Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
