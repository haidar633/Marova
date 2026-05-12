@push('style')
    <style>
        .activity-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .stat-icon {
            font-size: 2rem;
            opacity: 0.7;
            margin-bottom: 15px;
        }
        .stat-value {
            font-size: 2.25rem;
            font-weight: bold;
            line-height: 1.1;
        }
        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
            margin-top: 5px;
        }
        .stat-value.text-small {
            font-size: 1.5rem; /* For longer text like position names */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }
    </style>
@endpush

<div class="container-fluid py-4">
    <div class="activity-card">
        <h2 class="mb-3"><i class="fas fa-chart-pie me-2"></i>Activity Statistics</h2>

        {{-- Filters --}}
        <div class="filter-section">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label for="filter_date_from" class="form-label fw-bold">From Date</label>
                    <input type="date" id="filter_date_from" class="form-control border border-2 p-2" wire:model.live="filter_date_from">
                </div>
                <div class="col-md-4">
                    <label for="filter_date_to" class="form-label fw-bold">To Date</label>
                    <input type="date" id="filter_date_to" class="form-control border border-2 p-2" wire:model.live="filter_date_to">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100 mt-3 mt-md-0" wire:click="resetFilters">
                        <i class="fas fa-sync-alt me-1"></i> Reset to Current Month
                    </button>
                </div>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-heartbeat"></i></div>
                <div class="stat-value">{{ $stats['total_sex_attempts'] }}</div>
                <div class="stat-label">Total Sex Attempts</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-hand-paper"></i></div>
                <div class="stat-value">{{ $stats['total_masturbation_sessions'] }}</div>
                <div class="stat-label">Total Masturbations</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-star"></i></div>
                <div class="stat-value text-small d-flex align-items-center justify-content-center gap-2" title="{{ $stats['top_position'] }}">
                    @if($stats['top_position_image'])
                        <div class="shadow-sm overflow-hidden" style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.8);">
                            <img src="{{ asset('storage/' . $stats['top_position_image']) }}" alt="Top Position" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    @endif
                    <span>{{ $stats['top_position'] }}</span>
                </div>
                <div class="stat-label">Most Used Intimacy</div>
            </div>

        </div>
    </div>
</div>
