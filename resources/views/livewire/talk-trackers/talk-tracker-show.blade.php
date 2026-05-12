<div>
    <div class="container">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>Talk Tracker Entry Details</span>
                <a class="btn btn-primary" href="{{ route('talk-trackers') }}">Back</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Date:</strong> {{ $talkTracker->conversation_date->format('M d, Y') }}
                        @if($talkTracker->conversation_time)
                            <br><strong>Time:</strong> {{ \Carbon\Carbon::parse($talkTracker->conversation_time)->format('h:i A') }}
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Topic:</strong> {{ $talkTracker->topic }}
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>Connection Rating:</strong>
                        @if($talkTracker->connection_rating)
                            <div class="d-flex align-items-center gap-1 mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($talkTracker->connection_rating >= $i)
                                        <i class="fas fa-star" style="font-size: 2rem; color: #facc15;"></i>
                                    @elseif($talkTracker->connection_rating >= $i - 0.5)
                                        <i class="fas fa-star-half-alt" style="font-size: 2rem; color: #facc15;"></i>
                                    @else
                                        <i class="far fa-star" style="font-size: 2rem; color: #ccc;"></i>
                                    @endif
                                @endfor
                            </div>
                        @else
                            <span class="text-muted">No rating</span>
                        @endif
                    </div>
                </div>

                @if($talkTracker->moodJournal)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <strong>Linked Mood Journal:</strong> 
                            {{ $talkTracker->moodJournal->entry_date->format('M d, Y') }} - {{ $talkTracker->moodJournal->emotional_state }}
                        </div>
                    </div>
                @endif

                <div class="row mt-3">
                    <div class="col-md-12">
                        <strong>Resolution Summary:</strong>
                        <div class="mt-2 p-3 bg-light border-radius">
                            <p style="white-space: pre-wrap;">{{ $talkTracker->resolution_summary ?? 'No resolution recorded' }}</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('edit-talk-tracker', $talkTracker->id) }}" class="btn btn-success">Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>
