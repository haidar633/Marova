@php use Carbon\Carbon; @endphp
@push('style')
    <style>
        .detail-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .detail-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 2rem;
        }
        .detail-body {
            padding: 2rem;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        .detail-item {
            padding: 1rem;
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            border-radius: 8px;
        }
        .detail-label {
            font-size: 0.8rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .detail-value {
            font-size: 1rem;
            font-weight: 500;
            color: #333;
        }
        .positions-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }
        .position-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .position-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }
        .position-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .position-card-title {
            padding: 0.75rem 1rem;
            background-color: white;
            font-weight: 600;
            text-align: center;
        }
        .star-rating-display { display: flex; gap: 2px; }
        .text-star-yellow { color: #facc15; }
    </style>
@endpush

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="detail-card">
                <div class="detail-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-white mb-0">Attempt Details</h5>
                            <p class="text-white opacity-8 mb-0">
                                {{ Carbon::parse($attempt->attempt_date)->format('l, F j, Y') }} at {{ $attempt->attempt_time ?? 'N/A' }}
                            </p>
                        </div>
                        <a href="{{ route('attempts') }}" class="btn btn-outline-white mb-0">
                            <i class="material-icons text-sm">arrow_back</i> Back to List
                        </a>
                    </div>
                </div>

                <div class="detail-body">
                    <h6 class="mb-3">Overview</h6>
                    <div class="detail-grid mb-4">
                        <div class="detail-item">
                            <div class="detail-label">Category</div>
                            <div class="detail-value text-capitalize">{{ $attempt->attempt_type ?? 'N/A' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                <span class="badge {{ $attempt->successful ? 'bg-gradient-success' : 'bg-gradient-warning' }}">
                                    {{ $attempt->successful ? 'Successful' : 'Incomplete' }}
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Duration</div>
                            <div class="detail-value">{{ $attempt->duration_minutes ? $attempt->duration_minutes . ' mins' : '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Lubrication</div>
                            <div class="detail-value">{{ $attempt->lubrication_used ? 'Yes' : 'No' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Rating</div>
                            <div class="detail-value">
                                @if($attempt->satisfaction_rating)
                                    <div class="star-rating-display">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($attempt->satisfaction_rating >= $i)
                                                <i class="material-icons text-star-yellow">star</i>
                                            @elseif ($attempt->satisfaction_rating > $i - 1 && $attempt->satisfaction_rating < $i && round($attempt->satisfaction_rating * 2) % 2 != 0)
                                                <i class="material-icons text-star-yellow">star_half</i>
                                            @else
                                                <i class="material-icons text-secondary">star_border</i>
                                            @endif
                                        @endfor
                                        <span class="ms-2">({{ $attempt->satisfaction_rating }}/5)</span>
                                    </div>
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(!$attempt->successful && $attempt->attempt_reason)
                        <h6 class="mb-3">Reason for Incompletion</h6>
                        <p class="text-muted">{{ $attempt->attempt_reason }}</p>
                    @endif

                    @if($attempt->description)
                        <h6 class="mb-3">Description / Notes</h6>
                        <p class="text-muted fst-italic">"{{ $attempt->description }}"</p>
                    @endif

                    <hr class="my-4">

                    <h6 class="mb-3">Intimacy Used</h6>
                    @if(!empty($positionDetails))
                        <div class="positions-gallery">
                            @foreach($positionDetails as $position)
                                <div class="position-card">
                                    <img src="{{ asset('storage/' . $position['photo']) }}" alt="{{ $position['name'] }}">
                                    <div class="position-card-title">{{ $position['name'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No intimacy items were recorded for this attempt.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
