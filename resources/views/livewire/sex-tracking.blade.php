@php use Carbon\Carbon; @endphp
@push('style')
    <style>
        /* General Calendar Style */
        #calendar { background-color: white; border-radius: 8px; padding: 12px; }
        .fc-header-toolbar { padding: 10px; background: #f8f9fa; }
        .fc-event { cursor: pointer; }
        .fc-event-title.fc-sticky { font-weight: bold; color: white; }

        /* Default: Past and Today are Green */
        .fc-day-past, .fc-day-today {
            background-color: #e8f5e9 !important; /* Very Light Green */
        }

        /* Upcoming/Future days are White */
        .fc-day-future {
            background-color: #ffffff !important;
        }

        /* Sex Tracking Background Events */
        .fc-bg-event.sex-success {
            background-color: #66bb6a !important; /* Material Green */
            opacity: 1 !important;
        }
        .fc-bg-event.sex-failure {
            background-color: #ff5252 !important; /* Vibrant Red */
            opacity: 1 !important;
        }


        /* Modal & Form Styles */
        .modal-content {
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        [x-cloak] {
            display: none !important;
        }

        /* Custom Toggle Switch Colors */
        .form-check-input.custom-switch {
            background-color: #f43d2b; /* Reddish when off */
            border-color: #fa8072;
            transition: background-color 0.2s ease-in-out;
        }

        .form-check-input.custom-switch:checked {
            background-color: #90ee90 !important; /* Light green when on */
            border-color: #90ee90 !important;
        }

        /* Star Rating Styles */
        .star-container {
            transition: transform 0.1s ease;
        }

        .star-container:hover {
            transform: scale(1.1);
        }

        svg {
            pointer-events: none;
        }

        /* Selectize with Images Styles */
        .selectize-control.multi .selectize-input {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
            padding: 5px;
            min-height: 40px;
        }

        .selectize-control.multi .selectize-input > div {
            display: flex;
            align-items: center;
            padding: 2px 8px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .selectize-control.multi .selectize-input > div img, .selectize-control .selectize-dropdown .option img {
            width: 24px;
            height: 24px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 8px;
        }

        input[type="number"]::-webkit-inner-spin-button {
            opacity: 1;
        }

        input[type="number"] {
            text-align: center;
            font-size: 1.1rem;
        }

        textarea, input {
            border-radius: 6px !important;
            box-shadow: none !important;
        }

        label.form-label.fw-bold {
            font-size: 1rem;
            color: #333;
        }


    </style>
@endpush

<div class="container-fluid py-4">
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success text-white alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Calendar Container --}}
    <div wire:ignore>
        <div id='calendar'></div>
    </div>

    {{-- Add/Edit Attempt Modal --}}
    <div class="modal fade" id="attemptModal" tabindex="-1" wire:ignore.self data-bs-backdrop="static"
         data-bs-keyboard="false">
        <div class="modal-dialog modal-xl">
            <form wire:submit.prevent="saveAttempt">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title fw-bold text-white">
                            {{ $existingAttemptId ? 'Edit' : '' }} Attempt for <span
                                class="badge bg-white text-primary">{{ Carbon::parse($selectedDate)->format('D, M j, Y') }}</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body"
                         x-data="{ successful: @entangle('successful').live, rating: @entangle('satisfaction_rating') }">

                        {{-- Row 1: Toggles & Conditional Fields --}}
                        <div class="row mb-4 align-items-end">
                            <div class="col-md-3">
                                <label class="text-secondary fw-bold fs-5">Successful?</label>
                                <div class="form-check form-switch form-check-lg">
                                    <input type="checkbox" class="form-check-input custom-switch"
                                           style="transform: scale(1.5);" id="successful" x-model="successful"
                                           role="switch">
                                </div>
                            </div>
                            <div class="col-md-6" x-show="!successful" x-cloak>
                                <label for="attempt_reason" class="form-label">Reason for Failure</label>
                                <input type="text" id="attempt_reason" wire:model="attempt_reason" placeholder="On her period, not in the mood, etc."
                                       class="form-control border p-2">
                            </div>
                            <div class="col-md-6" x-show="successful" x-cloak>
                                <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                                <input type="number" min="0" id="duration_minutes" wire:model="duration_minutes"
                                       placeholder="ex. 30min" class="form-control border p-2">
                            </div>
                            <div class="col-md-3 ml-2 text-end" x-show="successful" x-cloak>
                                <label class="text-secondary fw-bold fs-5">Lubrication?</label>
                                <div class="form-check form-switch d-flex justify-content-end">
                                    <input type="checkbox" class="form-check-input custom-switch"
                                           style="transform: scale(1.5);" id="lubrication_used"
                                           wire:model="lubrication_used" role="switch">
                                </div>
                            </div>
                        </div>

                        {{-- Row 2: Date, Time, Type, Positions --}}
                        <div class="row">
                            <div class="col-md-6 mb-3"><label for="attempt_time" class="fw-bold">Attempt
                                    Time</label><input type="time" id="attempt_time" wire:model="attempt_time"
                                                       class="form-control border p-2"></div>
                            <div class="col-md-6 mb-3"><label for="attempt_type" class="fw-bold">Attempt
                                    Type</label><select id="attempt_type" wire:model="attempt_type"
                                                        class="form-control border p-2">
                                    <option value="">Select type...</option>
                                    <option value="intercourse">Intercourse</option>
                                    <option value="intimacy">Intimacy</option>
                                    <option value="foreplay">Foreplay</option>
                                    <option value="other">Other</option>
                                </select></div>
                            <div class="col-md-12 mb-3" wire:ignore>
                                <label class="fw-bold">Position(s)</label>
                                <select id="selectize-positions" multiple>
                                    @foreach($positionOptions as $position)
                                        <option
                                            value="{{ $position['id'] }}"
                                            data-photo="{{ asset('storage/' . $position['photo']) }}"
                                            data-name="{{ $position['name'] }}">
                                            {{ $position['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedPositions') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Row 3: Satisfaction Rating + Description --}}
                        {{-- Your Feedback in Card --}}
                        <div class="card shadow-sm border rounded-3 my-3">
                            <div class="card-header text-dark fw-bold" style="background-color: #facc15;">
                                <h4 class="text-white">Your Feedback</h4>
                            </div>

                            <div class="card-body">
                                <div class="row">

                                    {{-- Your Satisfaction Rating --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="d-block text-center fw-bold fs-6 mb-2">Your Satisfaction Rating</label>
                                        <div class="d-flex justify-content-center align-items-center gap-1" x-data="{
                                                    hoverRating: null,
                                                    rating: $wire.entangle('satisfaction_rating'),
                                                    setRating(value) {
                                                        this.rating = value;
                                                        $wire.satisfaction_rating = value;
                                                    },
                                                    getFill(index) {
                                                        const r = this.hoverRating ?? this.rating;
                                                        if (r >= index) return 'full';
                                                        if (r >= index - 0.5) return 'half';
                                                        return 'empty';
                                                    },
                                                    onMouseEnter(value) { this.hoverRating = value; },
                                                    onMouseLeave() { this.hoverRating = null; }
                                                }">
                                            <template x-for="i in [1,2,3,4,5]" :key="'mine-' + i">
                                                <div class="position-relative star-container" style="width: 45px; height: 45px; cursor: pointer;">
                                                    <!-- Half Star Click Zone -->
                                                    <div class="position-absolute top-0 start-0 h-100" style="width: 50%; z-index: 10;"
                                                         @click="setRating(i - 0.5)" @mouseenter="hoverRating = i - 0.5" @mouseleave="hoverRating = null"></div>
                                                    <!-- Full Star Click Zone -->
                                                    <div class="position-absolute top-0 end-0 h-100" style="width: 50%; z-index: 10;"
                                                         @click="setRating(i)" @mouseenter="hoverRating = i" @mouseleave="hoverRating = null"></div>

                                                    <!-- Outline Star -->
                                                    <svg class="position-absolute w-100 h-100" fill="none" stroke="#ccc" stroke-width="1.5" viewBox="0 0 24 24">
                                                        <path d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z"/>
                                                    </svg>

                                                    <!-- Full Fill -->
                                                    <template x-if="getFill(i) === 'full'">
                                                        <svg class="position-absolute w-100 h-100" fill="#facc15" stroke="#facc15" stroke-width="1.5" viewBox="0 0 24 24">
                                                            <path d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z"/>
                                                        </svg>
                                                    </template>

                                                    <!-- Half Fill -->
                                                    <template x-if="getFill(i) === 'half'">
                                                        <svg class="position-absolute w-100 h-100" viewBox="0 0 24 24">
                                                            <defs>
                                                                <linearGradient :id="'grad-mine-' + i" x1="0" x2="100%" y1="0" y2="0">
                                                                    <stop offset="50%" stop-color="#facc15"/>
                                                                    <stop offset="50%" stop-color="transparent"/>
                                                                </linearGradient>
                                                            </defs>
                                                            <path :fill="'url(#grad-mine-' + i + ')'" stroke="#facc15" stroke-width="1.5"
                                                                  d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z"/>
                                                        </svg>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Your Description --}}
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-secondary">Your Notes / Description</h6>
                                        <textarea id="description" wire:model="description"
                                                  class="form-control border p-2" rows="4"
                                                  placeholder="Any additional thoughts, emotions, or comments..."></textarea>
                                        @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Her Feedback in Card --}}
                        <div class="card shadow-sm border rounded-3 my-3">
                            <div class="card-header text-white fw-bold" style="background-color: #60a5fa;">
                                <h4 class="text-white">Her Feedback</h4>
                            </div>

                            <div class="card-body">
                                <div class="row">

                                    {{-- Her Satisfaction Rating --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="d-block text-center fw-bold fs-6 mb-2">Her Satisfaction Rating</label>
                                        <div class="d-flex justify-content-center align-items-center gap-1" x-data="{
                                                hoverRating: null,
                                                rating: $wire.entangle('her_rating'),
                                                setRating(value) {
                                                    this.rating = value;
                                                    $wire.her_rating = value;
                                                },
                                                getFill(index) {
                                                    const r = this.hoverRating ?? this.rating;
                                                    if (r >= index) return 'full';
                                                    if (r >= index - 0.5) return 'half';
                                                    return 'empty';
                                                },
                                                onMouseEnter(value) { this.hoverRating = value; },
                                                onMouseLeave() { this.hoverRating = null; }
                                            }">
                                            <template x-for="i in [1,2,3,4,5]" :key="'her-' + i">
                                                <div class="position-relative star-container" style="width: 45px; height: 45px; cursor: pointer;">
                                                    <!-- Half Star Click Zone -->
                                                    <div class="position-absolute top-0 start-0 h-100" style="width: 50%; z-index: 10;"
                                                         @click="setRating(i - 0.5)" @mouseenter="hoverRating = i - 0.5" @mouseleave="hoverRating = null"></div>
                                                    <!-- Full Star Click Zone -->
                                                    <div class="position-absolute top-0 end-0 h-100" style="width: 50%; z-index: 10;"
                                                         @click="setRating(i)" @mouseenter="hoverRating = i" @mouseleave="hoverRating = null"></div>

                                                    <!-- Outline Star -->
                                                    <svg class="position-absolute w-100 h-100" fill="none" stroke="#ccc" stroke-width="1.5" viewBox="0 0 24 24">
                                                        <path d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z"/>
                                                    </svg>

                                                    <!-- Full Fill -->
                                                    <template x-if="getFill(i) === 'full'">
                                                        <svg class="position-absolute w-100 h-100" fill="#60a5fa" stroke="#60a5fa" stroke-width="1.5" viewBox="0 0 24 24">
                                                            <path d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z"/>
                                                        </svg>
                                                    </template>

                                                    <!-- Half Fill -->
                                                    <template x-if="getFill(i) === 'half'">
                                                        <svg class="position-absolute w-100 h-100" viewBox="0 0 24 24">
                                                            <defs>
                                                                <linearGradient :id="'grad-her-' + i" x1="0" x2="100%" y1="0" y2="0">
                                                                    <stop offset="50%" stop-color="#60a5fa"/>
                                                                    <stop offset="50%" stop-color="transparent"/>
                                                                </linearGradient>
                                                            </defs>
                                                            <path :fill="'url(#grad-her-' + i + ')'" stroke="#60a5fa" stroke-width="1.5"
                                                                  d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z"/>
                                                        </svg>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Her Description --}}
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-secondary">Her Note / Comment</h6>
                                        <textarea id="her_description" wire:model="her_description"
                                                  class="form-control border p-2" rows="4"
                                                  placeholder="Her opinion, reaction, experience..."></textarea>
                                        @error('her_description') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                        <div>
                            @if($existingAttemptId)
                                <button type="button" class="btn btn-danger btn-link" data-original-title="" title="" onclick="confirmDelete({{ $existingAttemptId }})">
                                    <div class="ripple-container">Delete</div>
                                </button>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"><span
                                wire:loading.remove>Save Attempt</span><span wire:loading>Saving...</span></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
    <script>
        document.addEventListener('livewire:initialized', function () {
            const calendarEl = document.getElementById('calendar');
            const modalEl = document.getElementById('attemptModal');
            const modal = new bootstrap.Modal(modalEl);
            let selectizePosition = null;

            // --- SELECTIZE INITIALIZATION ---
            function initializePositionsSelectize() {
                let $select = $('#selectize-positions');
                if ($select[0]?.selectize) return; // Already initialized

                selectizePosition = $select.selectize({
                    plugins: ['remove_button'],
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    placeholder: 'Select positions...',
                    onChange: function (values) {
                        Livewire.dispatch('positionSelectize', [values]);
                    },
                    render: {
                        option: function (item, escape) {
                            const photo = escape(item.photo || item['data-photo'] || '');
                            const name = escape(item.name || item['data-name'] || '');
                            return `<div class="d-flex align-items-center">
                        <img src="${photo}" alt="photo" style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                        <span>${name}</span>
                    </div>`;
                        },
                        item: function (item, escape) {
                            const photo = escape(item.photo || item['data-photo'] || '');
                            const name = escape(item.name || item['data-name'] || '');
                            return `<div class="d-flex align-items-center">
                        <img src="${photo}" alt="photo" style="width: 20px; height: 20px; object-fit: cover; border-radius: 3px; margin-right: 6px;">
                        <span>${name}</span>
                    </div>`;
                        }
                    }
                })[0].selectize;

            }

            // --- FULLCALENDAR INITIALIZATION ---
            const calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek'},
                initialView: 'dayGridMonth',
                dayMaxEvents: true,
                events: (fetchInfo, successCallback) => @this.getEvents().then(e => successCallback(e)),
                dateClick: (info) => {
                @this.call('openModal', info.dateStr)
                     // fresh new attempt
                },
                eventClick: (info) => {
                    const eventId = info.event.id;

                    if (eventId.startsWith('attempt-')) {
                        const attemptId = eventId.replace('attempt-', '');
                    @this.call('openExistingAttempt', attemptId)

                    } else {
                    @this.call('openModal', info.event.startStr)

                    }
                },

                eventDidMount: (info) => {
                    const event = info.event;
                    if (event.extendedProps.successful) {
                        info.el.classList.add('fc-event-success');
                    } else {
                        info.el.classList.add('fc-event-failure');
                    }
                    if (event.extendedProps.lubrication_used) {
                        info.el.classList.add('fc-event-lubrication');
                    }
                },
                eventDidMount: function (info) {
                    if (info.event.extendedProps?.description) {
                        new bootstrap.Tooltip(info.el, {
                            title: info.event.extendedProps.description,
                            placement: 'top',
                        });
                    }
                },


                dayCellDidMount: function (info) {
                    // Add checkmark using the CSS pseudo-element
                    if (info.isPast && !info.el.querySelector('.fc-event-main, .fc-daygrid-bg-event')) {
                        info.el.classList.add('fc-day-past');
                    }
                }
            });
            calendar.render();
            initializePositionsSelectize();

            // --- LIVEWIRE & MODAL EVENT LISTENERS ---
        @this.on('show-modal', () => modal.show())


        @this.on('hide-modal-and-refetch', () => {
            modal.hide();
            calendar.refetchEvents();
        })


            // This is crucial: Load data into Selectize AFTER the modal is fully visible
            modalEl.addEventListener('shown.bs.modal', function () {
                if (selectizePosition) {
                    selectizePosition.setValue(@this.get('selectedPositions'), true);
                }
            });

        });
    </script>
@endpush
