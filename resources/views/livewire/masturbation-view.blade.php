@push('style')
    <style>
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

        /* Masturbation Entries are Red */
        .fc-bg-event.masturbation-entry {
            background-color: #ff5252 !important; /* Vibrant Red */
            opacity: 1 !important;
        }

        /* Star Rating Styles */
        .star-container { transition: transform 0.1s ease; }
        .star-container:hover { transform: scale(1.1); }
        svg { pointer-events: none; }
    </style>
@endpush

<div class="container-fluid py-4">
    {{-- Flash Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success text-white alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Calendar --}}
    <div wire:ignore><div id='calendar'></div></div>

    {{-- Add/Edit Entry Modal --}}
    <div class="modal fade" id="entryModal" tabindex="-1" wire:ignore.self data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <form wire:submit.prevent="saveEntry">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title fw-bold text-white">
                            {{ $existingEntryId ? 'Edit' : 'Log' }} Entry for <span class="badge bg-white text-primary">{{ \Carbon\Carbon::parse($selectedDate)->format('D, M j, Y') }}</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" x-data="{ rating: @entangle('rating') }">

                        {{-- Row 1: Time, Count, Vaseline --}}
                        <div class="row mb-3 align-items-end">
                            <div class="col-md-4">
                                <label for="entry_time" class="fw-bold">Time</label>
                                <input type="time" id="entry_time" wire:model="entry_time" class="form-control border p-2">
                            </div>
                            <div class="col-md-4">
                                <label for="count" class="fw-bold">Number of Times</label>
                                <input type="number" min="1" id="count" wire:model="count" class="form-control border p-2">
                            </div>
                            <div class="col-md-4 text-center">
                                <label class="fw-bold fs-5">Vaseline?</label>
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input type="checkbox" class="form-check-input" style="transform: scale(1.5);" id="vaseline_used" wire:model="vaseline_used" role="switch">
                                </div>
                            </div>
                        </div>

                        {{-- Row 2: Reason --}}
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="reason" class="fw-bold">Reason</label>
                                <input type="text" id="reason" wire:model="reason" class="form-control border p-2" placeholder="e.g., Stress, Boredom, etc.">
                            </div>
                        </div>

                        {{-- Row 3: Star Rating --}}
                        <hr class="my-4">
                        <label class="d-block text-center fw-bold fs-5 mb-3">Satisfaction Rating</label>
                        <div class="d-flex justify-content-center align-items-center gap-1" x-data="{ hoverRating: null, setRating(value) { rating = value; } }">
                            <template x-for="i in [1,2,3,4,5]" :key="i">
                                <div class="position-relative star-container" style="width: 50px; height: 50px; cursor: pointer;">
                                    <div class="position-absolute h-100" style="width: 50%; z-index: 10;" @click="setRating(i - 0.5)" @mouseenter="hoverRating = i - 0.5" @mouseleave="hoverRating = null"></div>
                                    <div class="position-absolute end-0 h-100" style="width: 50%; z-index: 10;" @click="setRating(i)" @mouseenter="hoverRating = i" @mouseleave="hoverRating = null"></div>
                                    <svg class="position-absolute w-100 h-100" fill="none" stroke="#ccc" viewBox="0 0 24 24"><path d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z"/></svg>
                                    <template x-if="(hoverRating ?? rating) >= i"><svg class="position-absolute w-100 h-100" fill="#facc15" stroke="#facc15" viewBox="0 0 24 24"><path d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z"/></svg></template>
                                    <template x-if="(hoverRating ?? rating) >= i - 0.5 && (hoverRating ?? rating) < i"><svg class="position-absolute w-100 h-100" viewBox="0 0 24 24"><defs><linearGradient :id="'grad-' + i"><stop offset="50%" stop-color="#facc15"/><stop offset="50%" stop-color="transparent"/></linearGradient></defs><path d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z" :fill="'url(#grad-' + i + ')'" stroke="#facc15"/></svg></template>
                                </div>
                            </template>
                        </div>

                        {{-- Row 4: Description --}}
                        <hr class="my-4">
                        <div class="row"><div class="col-12"><label for="description" class="fw-bold">Description / Notes</label><textarea id="description" wire:model="description" rows="4" class="form-control border p-2"></textarea></div></div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Save Entry</button>
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
            const modal = new bootstrap.Modal(document.getElementById('entryModal'));

            const calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },
                initialView: 'dayGridMonth',
                events: (info, success) => @this.getEvents().then(e => success(e)),

                dateClick: (info) => {
                    // Open a new, blank entry modal
                @this.openModal(info.dateStr);
                },
                eventClick: (info) => {
                    const eventId = info.event.id;
                    if (eventId.startsWith('entry-')) {
                        // Open an existing entry for editing
                    @this.openExistingEntry(eventId.replace('entry-', ''));
                    } else if (eventId.startsWith('checkmark-')) {
                        // Open a new entry if a checkmark is clicked
                    @this.openModal(info.event.startStr);
                    }
                },
                dayCellDidMount: function(info) {
                    // Add class to past, empty days to trigger the CSS checkmark
                    if (info.isPast && !info.el.querySelector('.fc-event')) {
                        info.el.classList.add('fc-day-past');
                    }
                }
            });
            calendar.render();

            // Livewire event listeners
        @this.on('show-modal', () => modal.show());
        @this.on('hide-modal-and-refetch', () => {
            modal.hide();
            calendar.refetchEvents();
        });
        });
    </script>
@endpush
