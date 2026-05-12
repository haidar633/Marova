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
                <span>Edit Vibe Check</span>
                <a class="btn btn-primary" href="{{ route('vibe-checks') }}">Back</a>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="update">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Status</label>
                                <select wire:model="status" class="form-control border border-2 p-2">
                                    <option value="Available for Connection">Available for Connection</option>
                                    <option value="Recharging/Busy">Recharging/Busy</option>
                                </select>
                                @error('status') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Context</label>
                                <textarea wire:model="context" class="form-control border border-2 p-2" rows="3"></textarea>
                                @error('context') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" wire:model="is_active" class="form-check-input">
                                <label class="form-check-label">Set as Active Status</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn bg-gradient-dark btn-md">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
