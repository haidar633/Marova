<div>
    <div class="container">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>Mood Journal Entry Details</span>
                <a class="btn btn-primary" href="{{ route('mood-journals') }}">Back</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Date:</strong> {{ $moodJournal->entry_date->format('M d, Y') }}
                        @if($moodJournal->entry_time)
                            <br><strong>Time:</strong> {{ \Carbon\Carbon::parse($moodJournal->entry_time)->format('h:i A') }}
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Emotional State:</strong> 
                        <span class="badge bg-gradient-{{ $moodJournal->emotional_state == 'Secure' ? 'success' : ($moodJournal->emotional_state == 'Anxious/Toxic' ? 'danger' : 'warning') }}">
                            {{ $moodJournal->emotional_state }}
                        </span>
                    </div>
                </div>

                @if($moodJournal->vibeCheck)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <strong>Linked Vibe Check:</strong> {{ $moodJournal->vibeCheck->status }}
                            @if($moodJournal->vibeCheck->context)
                                - {{ $moodJournal->vibeCheck->context }}
                            @endif
                        </div>
                    </div>
                @endif

                <div class="row mt-3">
                    <div class="col-md-12">
                        <strong>POV (Point of View):</strong>
                        <div class="mt-2 p-3 bg-light border-radius">
                            <p style="white-space: pre-wrap;">{{ $moodJournal->pov ?? 'No POV recorded' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <strong>Discussed with Partner:</strong>
                        @if($moodJournal->discussed_with_partner)
                            <i class="material-icons text-success">check_circle</i> Yes
                        @else
                            <i class="material-icons text-muted">cancel</i> No
                        @endif
                    </div>
                </div>

                @if($moodJournal->talkTrackers->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <strong>Related Talk Trackers:</strong>
                            <ul>
                                @foreach($moodJournal->talkTrackers as $talk)
                                    <li>{{ $talk->topic }} ({{ $talk->conversation_date->format('M d, Y') }})</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('edit-mood-journal', $moodJournal->id) }}" class="btn btn-success">Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>
