<div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('success') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('error') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="container">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>Create Mood Journal Entry</span>
                <a class="btn btn-primary" href="{{ route('mood-journals') }}">Back to Mood Journal</a>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="store">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entry_date">Entry Date</label>
                                <input type="date" id="entry_date" wire:model="entry_date" class="form-control border border-2 p-2">
                                @error('entry_date') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entry_time">Entry Time</label>
                                <input type="time" id="entry_time" wire:model="entry_time" class="form-control border border-2 p-2">
                                @error('entry_time') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="emotional_state">Emotional State</label>
                                <select id="emotional_state" wire:model="emotional_state" class="form-control border border-2 p-2">
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
                                @error('emotional_state') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vibe_check_id">Link to Vibe Check (Optional)</label>
                                <select id="vibe_check_id" wire:model="vibe_check_id" class="form-control border border-2 p-2">
                                    <option value="">None</option>
                                    @if($activeVibeCheck)
                                        <option value="{{ $activeVibeCheck->id }}">{{ $activeVibeCheck->status }} - {{ $activeVibeCheck->context ?? 'No context' }}</option>
                                    @endif
                                </select>
                                @error('vibe_check_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pov">POV (Point of View - Internal Thoughts)</label>
                                <textarea id="pov" wire:model="pov" class="form-control border border-2 p-2" rows="5" placeholder="Express your internal thoughts and feelings..."></textarea>
                                @error('pov') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" id="discussed_with_partner" wire:model="discussed_with_partner" class="form-check-input">
                                <label class="form-check-label" for="discussed_with_partner">Discussed with Partner</label>
                            </div>
                            @error('discussed_with_partner') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn bg-gradient-dark btn-md">
                            Save Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
