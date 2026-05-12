@php use Illuminate\Support\Str; @endphp
@php use Carbon\Carbon; @endphp
@push('style')
    <style>
        /* ... (all your existing styles are fine) ... */
        .star-rating {
            display: inline-flex;
            gap: 2px;
        }

        .star-container {
            position: relative;
            width: 20px;
            height: 20px;
            /* removed cursor pointer as it's display-only now */
        }

        .star-half::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            background: #facc15; /* This style is not used in the restored component but leaving for context */
            z-index: 1;
        }

        .text-star-yellow {
            color: #facc15;
        }

        .pyramid-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px; /* Space between rows */
        }
        .pyramid-row {
            display: flex;
            justify-content: center;
            gap: 4px; /* Space between badges in a row */
            flex-wrap: wrap; /* Allow wrapping for long names */
        }

        /* NEW STYLE: Ensure full position names look good */
        .pyramid-badge {
            white-space: nowrap;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        /* ... (all your other existing styles) ... */

    </style>
@endpush

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                {{-- ... (Keep Header, Add Button, and Filters section as is) ... --}}
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    @php
                        $userPreferences = auth()->user()->preferences()->first();
                        $activeColor = $userPreferences ? $userPreferences->sidebar_color : 'primary';
                    @endphp
                    <div class="bg-gradient-{{$activeColor}} shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white mx-3"><strong>Intimate Moments Tracker</strong></h6>
                    </div>
                </div>

                {{-- Add Button --}}
                <div class="me-3 my-3 text-end">
                    <a class="btn bg-gradient-dark mb-0" href="{{ route('add-attempt') }}">
                        <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Add New Attempt
                    </a>
                </div>

                {{-- Filters --}}
                <div class="filter-section mx-3">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="dateFrom" class="form-label">From Date</label>
                            <input type="date" wire:model.defer="dateFrom" class="form-control border border-2 p-2">
                        </div>
                        <div class="col-md-3">
                            <label for="dateTo" class="form-label">To Date</label>
                            <input type="date" wire:model.defer="dateTo" class="form-control border border-2 p-2">
                        </div>
                        <div class="col-md-2">
                            <label for="attemptType" class="form-label">Category</label>
                            <select wire:model.defer="attemptType" class="form-control border border-2 p-2">
                                <option value="">All Categories</option>
                                <option value="intercourse">Intercourse</option>
                                <option value="intimacy">Intimacy</option>
                                <option value="foreplay">Foreplay</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="successfulFilter" class="form-label">Status</label>
                            <select wire:model.defer="successfulFilter" class="form-control border border-2 p-2">
                                <option value="">All Status</option>
                                <option value="1">Successful</option>
                                <option value="0">Unsuccessful</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button wire:click="$refresh" class="btn btn-dark flex-fill">Apply</button>
                                <button wire:click="clearFilters" class="btn btn-outline-secondary">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Grouped Attempts --}}
                <div class="card-body px-3 pb-2">
                    <div class="accordion shadow-sm rounded-3" id="attemptsAccordion">
                        @foreach ($groupedAttempts as $date => $group)
                            @php $collapseId = 'collapse-' . Str::slug($date); @endphp

                            <div class="accordion-item border-0 mb-3">
                                <div class="card shadow-sm">
                                    <h2 class="accordion-header m-0" id="heading-{{ $collapseId }}">
                                        <button class="accordion-button collapsed px-4 py-3 bg-white shadow-none border-0 rounded"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#{{ $collapseId }}"
                                                aria-expanded="false" aria-controls="{{ $collapseId }}">
                                            <div class="d-flex justify-content-between w-100 align-items-center">
                                                <span class="h6 mb-0 fw-bold">{{ Carbon::parse($date)->format('l, F j, Y') }}</span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="{{ $collapseId }}" class="accordion-collapse collapse"
                                         aria-labelledby="heading-{{ $collapseId }}"
                                         data-bs-parent="#attemptsAccordion">
                                        <div class="accordion-body p-0">
                                            <div class="table-responsive border-top rounded shadow-sm m-3">
                                                <table class="table table-sm table-hover align-middle mb-0 border rounded">
                                                    <thead class="table-light">
                                                    <tr>
                                                        <th class="text-center text-xs">Type</th>
                                                        <th class="text-center text-xs">Time</th>
                                                        <th class="text-center text-xs">Duration</th>
                                                        <th class="text-center text-xs">Status</th>
                                                        <th class="text-center text-xs">Intimacy</th>
                                                        <th class="text-center text-xs">Rating</th>
                                                        <th class="text-center text-xs">Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($group as $attempt)
                                                        <tr>
                                                            <td class="text-center">{{ ucfirst($attempt->attempt_type) }}</td>
                                                            <td class="text-center">{{ $attempt->attempt_time ? Carbon::parse($attempt->attempt_time)->format('h:i A') : '—' }}</td>
                                                            <td class="text-center">{{ $attempt->duration_minutes ? $attempt->duration_minutes . ' mins' : '—' }}</td>
                                                            <td class="text-center">
                                                                <span class="badge {{ $attempt->successful ? 'bg-success' : 'bg-warning' }}">
                                                                    {{ $attempt->successful ? 'Successful' : 'Incomplete' }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center px-2">
                                                                @php
                                                                    $decodedIds = json_decode($attempt->positions, true) ?? [];
                                                                    $attemptPositions = [];
                                                                    if(!empty($decodedIds)) {
                                                                        foreach($decodedIds as $pid) {
                                                                            // CHANGED: Simplified lookup using keyBy('id')
                                                                            if(isset($positionsMap[$pid])) {
                                                                                $attemptPositions[] = $positionsMap[$pid];
                                                                            }
                                                                        }
                                                                    }

                                                                    // Pyramid logic
                                                                    $chunks = [];
                                                                    if (!empty($attemptPositions)) {
                                                                        $offset = 0;
                                                                        $rowSize = 1;
                                                                        while ($offset < count($attemptPositions)) {
                                                                            $chunks[] = array_slice($attemptPositions, $offset, $rowSize);
                                                                            $offset += $rowSize;
                                                                            $rowSize++;
                                                                        }
                                                                        // CHANGED: Invert the pyramid
                                                                        $chunks = array_reverse($chunks);
                                                                    }
                                                                @endphp

                                                                @if(!empty($chunks))
                                                                    <div class="pyramid-container">
                                                                        @foreach($chunks as $row)
                                                                            <div class="pyramid-row">
                                                                                @foreach($row as $position)
                                                                                    {{-- CHANGED: Display full name and add new class --}}
                                                                                    <span class="badge bg-gradient-warning text-white pyramid-badge" title="{{ $position['name'] }}">
                                                                                        {{ $position['name'] }}
                                                                                    </span>
                                                                                @endforeach
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <!-- RESTORED & SIMPLIFIED: Star rating display -->
                                                                <div x-data="{ rating: {{ $attempt->satisfaction_rating ?? 0 }} }"
                                                                     class="d-flex justify-content-center gap-1 star-rating">
                                                                    <template x-for="i in [1, 2, 3, 4, 5]" :key="i">
                                                                        <div class="star-container">
                                                                            <svg
                                                                                :class="{
                                                                                    'text-star-yellow': rating >= i,
                                                                                    'text-secondary': rating < (i - 0.5)
                                                                                }"
                                                                                width="20" height="20"
                                                                                fill="currentColor" viewBox="0 0 24 24">
                                                                                <path d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z"/>
                                                                            </svg>
                                                                            <template x-if="rating >= (i - 0.5) && rating < i">
                                                                                <div class="position-absolute top-0 start-0 overflow-hidden" style="width: 50%;">
                                                                                    <svg class="text-star-yellow" width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                                                                        <path d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z"/>
                                                                                    </svg>
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                @can('attempt-show')
                                                                    <a href="{{ route('view-attempt', $attempt->id) }}"
                                                                       class="btn btn-sm btn-info me-1" title="View Details">
                                                                        <i class="material-icons" style="font-size: 1rem;">visibility</i>
                                                                    </a>
                                                                @endcan
                                                                @can('attempt-edit')
                                                                    <a href="{{ route('edit-attempt', $attempt->id) }}"
                                                                       class="btn btn-sm btn-success me-1" title="Edit">
                                                                        <i class="material-icons" style="font-size: 1rem;">edit</i>
                                                                    </a>
                                                                @endcan
                                                                @can('attempt-delete')
                                                                    <button type="button" class="btn btn-sm btn-danger" title="Delete"
                                                                            onclick="confirmDelete({{ $attempt->id }})">
                                                                        <i class="material-icons" style="font-size: 1rem;">delete</i>
                                                                    </button>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div> <!-- accordion-body -->
                                    </div> <!-- collapse -->
                                </div> <!-- card -->
                            </div> <!-- accordion-item -->
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
