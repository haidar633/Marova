@push('style')
    <style>
        @media (max-width: 767.98px) {

            .modal-dialog.modal-xl,
            .modal-dialog.modal-m {
                max-width: 100%;
                margin: 0;
            }

            .modal-content {
                border-radius: 0;
                height: 100vh;
                overflow-y: auto;
            }

            .fc .fc-toolbar.fc-header-toolbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .fc-toolbar-title {
                font-size: 1rem;
            }

            .fc .fc-button {
                font-size: 0.85rem;
                padding: 0.375rem 0.5rem;
            }
        }

        .fc-event-time {
            display: none;
        }


        .fc-day-doctor-unavailable:hover {
            background-color: #f0f0f0 !important;
        }

        .fc-daygrid-day:not(.fc-day-doctor-unavailable):not(.fc-day-other) {
            background-color: #ffffff !important;
        }

        .fc-day-today:not(.fc-day-doctor-unavailable) {
            background-color: rgba(255, 220, 40, 0.15) !important;
        }


        .custom-timegrid-dot {
            display: inline-block;
        }


        .fc-timegrid-event-harness .fc-event {
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }


        /* Optional: reset hover */
        .fc-timegrid-event:hover {
            filter: brightness(95%);
            background-color: transparent !important;
        }


        .fc-day-doctor-unavailable .fc-daygrid-day-frame,
        .fc-day-non-working .fc-daygrid-day-frame {
            background: inherit;
        }

        .fc-day-today {
            background-color: rgba(255, 220, 40, 0.15) !important;
        }

        .fc-event {
            z-index: 3;
            position: relative;
        }


        .fc {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .fc-header-toolbar {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            margin-bottom: 15px !important;
        }

        .fc-event-title strong {
            font-weight: bold !important;
        }

        .fc-event-title span {
            font-weight: normal !important;
        }


        .fc .fc-button {
            font-weight: 500;
            box-shadow: none;
            transition: all 0.2s;
        }

        .fc .fc-button-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .fc .fc-button-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }

        #calendar {
            background-color: white;
            border-radius: 6px;
            padding: 8px;
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075);
        }


        .doctor-filter-active .selectize-input {
            background-color: #e8f0fe;
            border-color: #0d6efd;
        }

        .selectize-input {
            border: 1px solid #ced4da !important;
            padding: 0.375rem 0.75rem !important;
            border-radius: 0.25rem !important;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            min-height: 38px;
        }

        .selectize-input.focus {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }


        .duty-event {
            border: none;
            color: #333 !important;
            padding: 4px 8px 4px 18px !important;
            border-radius: 3px;
            font-size: 0.9em;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            position: relative;
        }

        .fc-daygrid-event-dot,
        .fc-list-event-dot,
        .fc-timegrid-event-dot {
            height: 44px !important;
            border-width: 4px !important;
        }


        .duty-event:hover {
            filter: brightness(90%);
            transform: scale(1.02);
        }


        .fc-daygrid-event {
            width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            border-radius: 3px;
            box-sizing: border-box;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .fc-daygrid-dot-event {
            width: 100% !important;
            display: flex !important;
            align-items: center;
        }

        .fc-event-title {
            flex-grow: 1;
            padding-right: 6px;
        }

        .fc-event-main {
            width: 100%;
        }

        .fc-daygrid-day-events {
            padding: 2px !important;
            margin: 0 !important;
        }

        .fc-content {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tooltip-inner {
            max-width: 300px;
            text-align: left;
            white-space: pre-line;
        }

        .fc-event {
            cursor: pointer;
        }

        .fc-timegrid-slot {
            cursor: pointer;
        }

        .fc-day-disabled {
            background-color: #f8f9fa;
            opacity: 0.6;
        }

        .modal-content {
            border: none;
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
        }

        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
@endpush
<div class="container-fluid py-4">
    @if(auth()->user()->type !== 'Doctor')
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <div class="mb-2">
                            <label for="filter_doctor_id" class="form-label fw-bold">Filter by Doctor</label>
                            <div wire:ignore>
                                <select class="selectize-doctor-filter" id="filter_doctor_id"
                                        wire:model.live="filter_doctor_id">
                                    <option value="">All Doctors</option>
                                    @foreach($allDoctors as $doctor)
                                        <option value="{{ $doctor->id }}">
                                            Dr. {{ $doctor->fname }}
                                            {{ $doctor->lname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="button" class="btn btn-outline-secondary mb-3" wire:click="resetFilter">
                    <i class="fas fa-sync-alt me-2"></i> Reset Filter
                </button>
            </div>
        </div>
    @endif
    <div wire:ignore>
        <div id='calendar' class="shadow-sm"></div>
    </div>
    <!-- Modal -->
    @can('appointment-create')
        <div class="modal fade" id="scheduleModal" tabindex="-1" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
            <div
                class="modal-dialog {{ ($event_id && (auth()->user()->can('appointment-edit'))) ? 'modal-xl' : 'modal-m' }}">
                <form wire:submit.prevent="saveSchedule">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold">Schedule Appointment</h5>
                            <button type="button" class="btn-close" wire:click="$dispatch('closeModal')" style="filter: brightness(0);"></button>

                        </div>
                        <div class="modal-body">
                            @if (count($errors) > 0)
                                <div class="alert alert-danger alert-dismissible text-white" role="alert">
                            <span class="text-lg">
                                Opps!
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </span>
                                    <button type="button" class="btn-close text-lg py-3 opacity-10"
                                            data-bs-dismiss="alert"
                                            aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <!-- Availability Error Alert -->
                            @if ($availabilityError)
                                <div class="alert alert-warning text-white">
                                    <strong>Availability Issue:</strong> {{ $availabilityError }}
                                </div>
                            @endif

                            <div class="row">
                                <!-- Patient/Doctor Information Section -->
                                <div
                                    class="{{ ($event_id && (auth()->user()->can('appointment-edit')))  ? 'col-md-6' : 'col-md-12' }}">
                                    <div class="card border-light mb-3">
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="doctor_id" class="form-label fw-bold">Doctor *</label>
                                                <div class="@error('doctor_id') is-invalid @enderror">
                                                    <div wire:ignore>
                                                        <select class="selectize-doctor" id="doctor_id"
                                                                wire:model.live="doctor_id">
                                                            <option value="">Select Doctor</option>
                                                            @foreach($doctors as $doctor)
                                                                <option value="{{ $doctor->id }}"
                                                                        @if(!$doctor->dr_is_active && auth()->id() !== $doctor->id) disabled @endif>
                                                                    Dr. {{ $doctor->fname }} {{ $doctor->lname }}
                                                                </option>
                                                            @endforeach
                                                        </select>

                                                    </div>
                                                </div>
                                                @error('doctor_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>



                                            <div class="mb-3">
                                                <label for="patient_id" class="form-label fw-bold">Patient *</label>


                                                <div class="@error('patient_id') is-invalid @enderror">

                                                    @if(!empty($searchTerm) && $patients->where('fname', 'like', "%{$searchTerm}%")->isEmpty())
                                                        <div class="mb-2">
                                                            <button type="button" class="btn btn-sm btn-primary" wire:click="showAddPatient">
                                                                <i class="fas fa-user-plus me-1"></i> Add Patient: "{{ $searchTerm }}"
                                                            </button>
                                                        </div>
                                                    @endif

                                                    <div wire:ignore>
                                                        <select class="selectize-patient" id="patient_id"
                                                                wire:model.live="patient_id" {{ ($event_id || !auth()->user()->can('appointment-edit')) ? 'disabled' : '' }}>
                                                            <option value="">Select Patient</option>
                                                            @foreach($patients as $patient)
                                                                <option value="{{ $patient->id }}">
                                                                    {{ $patient->fname }} {{ $patient->lname }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                                @error('patient_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Start Time *</label>
                                                        <div
                                                            class="@error('start_time') border border-danger rounded-3 @enderror">
                                                            <input type="time" class="form-control border p-2"
                                                                   wire:model.live="start_time"
                                                                   @cannot('appointment-edit') disabled @endcannot />
                                                        </div>
                                                        @error('start_time')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">End Time *</label>
                                                        <div
                                                            class="@error('end_time') border border-danger rounded-3 @enderror">
                                                            <input type="time" class="form-control border p-2"
                                                                   wire:model.live="end_time"
                                                                   @cannot('appointment-edit') disabled @endcannot />
                                                        </div>
                                                        @error('end_time')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Description *</label>
                                                <div
                                                    class="@error('description') border border-danger rounded-3 @enderror">
                                                <textarea class="form-control border p-2"
                                                          wire:model.defer="description"
                                                          @cannot('appointment-edit') disabled @endcannot></textarea>
                                                </div>
                                                @error('description')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-10 mt-3">
                                                    <label for="doctor_color">Doctor Color</label>
                                                    <input wire:model="doctor_color"
                                                           type="color"
                                                           class="form-control form-control-color border border-2 p-2"
                                                           id="doctor_color"
                                                        {{ auth()->user()->can('appointment-edit') ? '' : 'disabled' }}>
                                                    @error('doctor_color')
                                                    <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">This color will be used for all of this
                                                        doctor's appointments.</small>
                                                </div>
                                            </div>

                                            <div class="mt-4 mt-3">
                                                @if($patient_id)
                                                    @can('patient-emrList')
                                                        <a rel="tooltip" class="btn btn-github btn-link"
                                                           data-original-title=""
                                                           title="Go to EMR" wire:click="goToEmr({{ $patient_id }})">
                                                            <i class="fas fa-lg fa-file-medical-alt ps-2 pe-2 text-center"></i>
                                                            <div class="ripple-container"></div>
                                                        </a>
                                                    @endcan

                                                    @can('patient-viewHistory')
                                                        <a rel="tooltip" class="btn btn-info btn-link"
                                                           href="{{ route('patient-appointments', $patient_id) }}">
                                                            <i class="fas fa-lg fa-eye ps-2 pe-2 text-center"></i>
                                                        </a>
                                                    @endcan
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($event_id)
                                    <div class="col-md-6">
                                        @can('appointment-edit')
                                            <div class="card border-light mb-3" wire:key="patient-arrived-switch-card">
                                                <div
                                                    class="card-header bg-light d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 fw-bold">Patient Arrived</h6>
                                                    <div class="form-check form-switch fs-4" style="transform: scale(1.3); transform-origin: left;">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="patientArrivedSwitch"
                                                               wire:model="patient_arrived"
                                                               @if($patient_arrived) checked @endif>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-light mb-3" wire:key="patient-arrived-data-card">
                                                <div class="card-body">
                                                    <!-- Services -->
                                                    <div class="mb-3">
                                                        <label wire:key="service-select" for="service_ids" class="form-label fw-bold">Services
                                                            *</label>
                                                        <div class="@error('service_ids') is-invalid @enderror">
                                                            <div wire:ignore>
                                                                <select class="selectize-service" id="service_ids"
                                                                        wire:model.live="service_ids" multiple>
                                                                    <option value="">Select Services</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        @error('service_ids')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    @if(!empty($selectedServices))
                                                        <div>
                                                            @foreach($selectedServices as $index => $service)
                                                                <div class="row mb-2 align-items-center service-row">
                                                                    <div class="col-6">
                                                                        <input type="text" class="form-control border p-2"
                                                                               disabled
                                                                               value="{{ $service['description'] }}">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input type="text" class="form-control border p-2"
                                                                               wire:model.live="selectedServices.{{ $index }}.price"
                                                                               wire:change="calculateTotals">
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif


                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Doctor Notes *</label>
                                                        <div
                                                            class="@error('doctor_description') border border-danger rounded-3 @enderror">
                                                <textarea class="form-control border p-2"
                                                          wire:model.defer="doctor_description"></textarea>
                                                        </div>
                                                        @error('doctor_description')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Totals -->
                                                    <div class="alert alert-info text-white">
                                                        <strong>Total:</strong>
                                                        ${{ number_format($totalServicesAmount, 2) }}<br>
                                                        <strong>Paid:</strong> ${{ number_format($paid, 2) }}
                                                    </div>

                                                    <!-- Payment Amount -->
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Amount *</label>
                                                        <div
                                                            class="@error('payment_amount') border border-danger rounded-3 @enderror">
                                                            <input type="number" class="form-control border p-2"
                                                                   wire:model="payment_amount" />
                                                        </div>
                                                        @error('payment_amount')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Payment Description -->
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Payment Description *</label>
                                                        <div
                                                            class="@error('payment_description') border border-danger rounded-3 @enderror">
                                                <textarea class="form-control border p-2"
                                                          wire:model.defer="payment_description"></textarea>
                                                        </div>
                                                        @error('payment_description')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan
                                    </div>

                            </div>
                            @endif
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-dark" wire:click="$dispatch('closeModal')">
                                    Close
                                </button>
                                @can('appointment-delete')
                                    @if($event_id)
                                        <button type="button" class="btn btn-danger"
                                                onclick="confirmDelete({{ $event_id }})">
                                            Delete
                                        </button>
                                    @endif
                                @endcan
                                @canany(['appointment-edit','appointment-create'])
                                    <button type="submit" class="btn btn-primary"
                                            @if($availabilityError) disabled @endif>
                                        Save
                                    </button>
                                @endcanany

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan

</div>

<!-- Place this anywhere in your appointments.blade.php -->
<div wire:ignore.self class="modal fade" id="addPatientModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title mb-0">Add Patient</h5>
                <button type="button" class="btn-close" wire:click="$dispatch('closeAddPatientModal')" style="filter: brightness(0);"></button>
            </div>

            <div class="modal-body p-0">
                @livewire('patients.patient-add', ['initialName' => $initialName], key('add-patient-modal'))
            </div>
        </div>

    </div>
</div>

@push('js')
    <script>
        window.isMobileView = @json($isMobile);

        function applyDoctorColors() {
            setTimeout(() => {
                // Apply to all event types across all views
                const eventDots = document.querySelectorAll('.fc-daygrid-event-dot, .fc-list-event-dot, .fc-timegrid-event-dot');
                eventDots.forEach(dot => {
                    const eventEl = dot.closest('.fc-event');
                    if (eventEl) {
                        const doctorId = eventEl.getAttribute('data-doctor-id');
                        const doctorColor = eventEl.getAttribute('data-doctor-color');
                        if (doctorColor) {
                            dot.style.borderColor = doctorColor;
                            // Ensure consistent styling
                            dot.style.height = '44px';
                            dot.style.borderWidth = '10px';
                        }
                    }
                });
            }, 300);
        }

        document.addEventListener('livewire:initialized', function() {
            let calendarEl = document.getElementById('calendar');
            let modal;
            let calendar;

            // Function to generate a random color
            function getRandomColor() {
                const letters = '0123456789ABCDEF';
                let color = '#';
                for (let i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }

            Livewire.on('markUnavailableDate', (dateStr) => {
                const cell = document.querySelector(`.fc-daygrid-day[data-date="${dateStr}"]`);
                if (cell) {
                    cell.classList.add('fc-day-doctor-unavailable');
                }
            });

            // Livewire.on('showAddPatientModal', () => {
            //     const modal = new bootstrap.Modal(document.getElementById('addPatientModal'), {
            //         backdrop: 'static',
            //         keyboard: false
            //     });
            //     modal.show();
            // });

            Livewire.on('closeAddPatientModal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPatientModal'));
                if (modal) {
                    modal.hide();
                }

                // Reset searchTerm and UI state in Livewire
                Livewire.dispatch('resetAddPatientState');

                // Clear the Selectize patient search field fully
                if ($(".selectize-patient")[0]?.selectize) {
                    $(".selectize-patient")[0].selectize.clear();
                    $(".selectize-patient")[0].selectize.clearOptions();
                }
            });



            // Apply random colors to event dots
            function applyRandomColors() {
                setTimeout(() => {
                    const eventDots = document.querySelectorAll('.fc-daygrid-event-dot');
                    // eventDots.forEach(dot => {
                    //     dot.style.borderColor = getRandomColor();
                    // });
                }, 300);
            }

            function initCalendar() {
                const isMobile = window.innerWidth < 768;
                calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: isMobile ?
                            'timeGridDay,listDay' // Mobile: only Day and List views
                            :
                            'dayGridMonth,timeGridWeek,timeGridDay,listMonth' // Desktop
                    },
                    initialView: isMobile ? 'timeGridDay' : 'dayGridMonth',
                    slotMinTime: '08:00:00',
                    slotMaxTime: '20:00:00',
                    slotDuration: '00:15:00', // Show 15-minute slots for more precise appointment scheduling
                    slotLabelInterval: '01:00',
                    allDaySlot: false,
                    editable: false,
                    events: function(info, successCallback, failureCallback) {
                    @this.getEvents().then(events => {
                        successCallback(events);
                        // Apply random colors after events are loaded
                        applyRandomColors();
                    }).catch(error => {
                        failureCallback(error);
                        console.error('Error fetching events:', error);
                    })
                    },
                    eventDidMount: function(info) {
                        const color = info.event.extendedProps.color || '#666';
                        const doctor = info.event.extendedProps.doctor || '';
                        const patient = info.event.extendedProps.patient || '';
                        const time = info.event.extendedProps.time || '';
                        const viewType = info.view.type;

                        if (['timeGridDay', 'timeGridWeek'].includes(viewType)) {
                            // Day/Week Views (time + bold names)
                            info.el.innerHTML = `
            <div class="d-flex" style="height: 100%;">
                <div style="width: 6px; background-color: ${color}; border-radius: 6px; margin-right: 8px;"></div>
                <div class="d-flex flex-column justify-content-center" style="line-height: 1.4;">
                    <div style="font-weight: bold;">Dr. ${doctor}</div>
                    <div style="font-weight: normal;">${time}</div>
                    <div style="font-weight: bold;">${patient}</div>
                </div>
            </div>
        `;

                            info.el.style.backgroundColor = "#fff";
                            info.el.style.border = "1px solid #e0e0e0";
                            info.el.style.borderRadius = "6px";
                            info.el.style.padding = "6px 8px";
                            info.el.style.boxShadow = "0 1px 2px rgba(0,0,0,0.05)";
                            info.el.style.fontSize = "0.9rem";
                        } else if (viewType === 'listMonth' || viewType.startsWith('list')) {
                            // List View — Only doctor and patient (no time)
                            const anchor = info.el.querySelector('a');
                            if (anchor) {
                                anchor.innerHTML = `
                <strong>Dr. ${doctor}</strong><br>
                <strong>${patient}</strong>
            `;
                            }

                            const dot = info.el.querySelector('.fc-list-event-dot');
                            if (dot && color) {
                                dot.style.borderColor = color;
                                dot.style.borderWidth = '10px';
                                dot.style.height = '44px';
                            }
                        } else {
                            // Month View — show time + bold names
                            const titleEl = info.el.querySelector('.fc-event-title');
                            if (titleEl) {
                                titleEl.innerHTML = `
                <strong>Dr. ${doctor}</strong><br>
                <span style="font-weight: normal;">${time}</span><br>
                <strong>${patient}</strong>
            `;
                            }

                            const dot = info.el.querySelector('.fc-daygrid-event-dot');
                            if (dot && color) {
                                dot.style.borderColor = color;
                                dot.style.borderWidth = '10px';
                                dot.style.height = '44px';
                            }
                        }
                    }


                    ,


                    dateClick: function(info) {
                        const viewType = info.view.type;

                        // Only apply for views where time matters
                        if (['timeGridDay', 'timeGridWeek'].includes(viewType)) {
                        @this.openModalWithTime(info.dateStr)

                        } else if (viewType === 'dayGridMonth') {
                        @this.openModal(info.dateStr)

                        }

                        modal = new bootstrap.Modal(document.getElementById('scheduleModal'));
                        modal.show();
                    },

                    eventClick: function(info) {
                    @this.loadEventData({
                        id: info.event.id,
                        doctor_id: info.event.extendedProps.doctor_id,
                        patient_id: info.event.extendedProps.patient_id,
                        service_id: info.event.extendedProps.service_id,
                        start: info.event.startStr,
                        end: info.event.endStr,
                        description: info.event.extendedProps.description,
                        doctor_description: info.event.extendedProps.doctor_description
                    })
                        modal = new bootstrap.Modal(document.getElementById('scheduleModal'));
                        modal.show();
                    },
                    // Custom Business Hours based on doctor availability
                    businessHours: @this.doctorBusinessHours || [{
                        daysOfWeek: [1, 2, 3, 4, 5], // Monday - Friday
                        startTime: '09:00',
                        endTime: '17:00'
                    }],
                    // Color days based on doctor availability
                    dayCellDidMount: function(info) {
                        const today = new Date();
                        const cellDate = info.date;
                        const frame = info.el.querySelector('.fc-daygrid-day-frame');

                        // 1. Gray out TODAY
                        const isToday =
                            today.getFullYear() === cellDate.getFullYear() &&
                            today.getMonth() === cellDate.getMonth() &&
                            today.getDate() === cellDate.getDate();

                        if (isToday && frame) {
                            frame.style.backgroundColor = '#e0e0e0';
                        }

                        // 2. Gray out non-available days (based on doctor availability)
                        if (@this.filter_doctor_id && @this.doctorBusinessHours) {
                            const dayOfWeek = cellDate.getDay();
                            const availableDays = @this.doctorBusinessHours.map(b => b.daysOfWeek).flat();

                            if (!availableDays.includes(dayOfWeek)) {
                                info.el.classList.add('fc-day-invalid');
                            }
                        }

                        // 3. Also gray out if there is an availabilityError from Livewire
                        const selectedDate = new Date(@this.selectedDate);
                        const isSelectedDate =
                            selectedDate.getFullYear() === cellDate.getFullYear() &&
                            selectedDate.getMonth() === cellDate.getMonth() &&
                            selectedDate.getDate() === cellDate.getDate();

                        if (isSelectedDate && @this.availabilityError) {
                            info.el.classList.add('fc-day-invalid');
                        }
                    },
                    // Ensure month view renders correctly
                    dayHeaderDidMount: function(info) {
                        // Add custom styling to day headers if needed
                        if (@this.filter_doctor_id && @this.doctorBusinessHours) {
                            let isAvailable = false;

                            // Check if this day is in any of the doctor's available days
                            for (let i = 0; i < @this.doctorBusinessHours.length; i++) {
                                let hours = @this.doctorBusinessHours[i];
                                if (hours.daysOfWeek && hours.daysOfWeek.includes(info.date.getDay())) {
                                    isAvailable = true;
                                    break;
                                }
                            }

                            if (!isAvailable) {
                                // If day of week is not available, style the header
                                info.el.classList.add('fc-day-header-unavailable');
                            }
                        }
                    },
                    // Better date formatting
                    slotLabelFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    },
                    // Additional visual enhancements
                    nowIndicator: true,
                    slotEventOverlap: false
                });

                calendar.render();

                // Apply random colors after initial render
                applyRandomColors();

                // Set up observer to detect dynamically added events
                const observer = new MutationObserver(function(mutations) {
                    applyRandomColors();
                });

                // Start observing calendar for changes
                observer.observe(calendarEl, {
                    childList: true,
                    subtree: true
                });
            }

            // Initialize calendar
            initCalendar();
            initializePageSelectize();

            // Refresh calendar when events are updated
            Livewire.on('scheduleAdded', () => {
                calendar.refetchEvents();
                // Apply random colors after event update
                // applyRandomColors();
                applyDoctorColors();

            });

            Livewire.on('updateUnavailableDates', (dates) => {
                const cells = document.querySelectorAll('.fc-daygrid-day');
                cells.forEach(cell => {
                    const date = cell.getAttribute('data-date');
                    if (dates.includes(date)) {
                        cell.classList.add('fc-day-doctor-unavailable');
                    } else {
                        cell.classList.remove('fc-day-doctor-unavailable');
                    }
                });
            });

            Livewire.on('enableSelectizeFields', () => {
                setTimeout(() => {
                    if ($(".selectize-doctor")[0]?.selectize) {
                        $(".selectize-doctor")[0].selectize.enable();
                        $(".selectize-doctor")[0].selectize.clear();
                    }
                    if ($(".selectize-patient")[0]?.selectize) {
                        $(".selectize-patient")[0].selectize.enable();
                        $(".selectize-patient")[0].selectize.clear();
                    }
                }, 100);
            });

            Livewire.on('filterChanged', () => {

                Livewire.dispatch('markUnavailableDates');


                // Refresh events with the new filter
                calendar.refetchEvents();

                if (@this.filter_doctor_id) {
                    // Visual indicator for filtered view
                    document.getElementById('calendar').classList.add('filtered-view');

                    if (@this.doctorBusinessHours && @this.doctorBusinessHours.length) {
                        // Set the business hours based on doctor availability
                        calendar.setOption('businessHours', @this.doctorBusinessHours);

                        // Extract the days the doctor is available
                        let availableDays = [];
                        @this.doctorBusinessHours.forEach(hour => {
                            if (hour.daysOfWeek) {
                                availableDays = [...availableDays, ...hour.daysOfWeek];
                            }
                        })


                            // Find days doctor is NOT available (0=Sunday, 6=Saturday)
                        let allDays = [0, 1, 2, 3, 4, 5, 6];
                        let hiddenDays = allDays.filter(day => !availableDays.includes(day));

                        // Set non-business days display
                        calendar.setOption('selectConstraint', 'businessHours');
                        calendar.setOption('displayEventTime', true);
                        calendar.setOption('nowIndicator', true);
                        calendar.setOption('slotEventOverlap', false);

                        // Important - set which days should be grayed out completely
                        calendar.setOption('hiddenDays', []); // Don't hide days, just gray them out
                        calendar.setOption('showNonCurrentDates', true);

                        // Add custom rendering for days doctor is not available
                        calendar.setOption('dayCellDidMount', function(info) {
                            // If this day of week is not in available days, mark as unavailable
                            if (!availableDays.includes(info.date.getDay())) {
                                info.el.classList.add('fc-day-doctor-unavailable');
                            }
                        });
                    } else {
                        // Default business hours if no specific availability
                        calendar.setOption('businessHours', [{
                            daysOfWeek: [1, 2, 3, 4, 5], // Monday - Friday
                            startTime: '09:00',
                            endTime: '17:00'
                        }]);
                        calendar.setOption('selectConstraint', 'businessHours');
                        calendar.setOption('displayNonBusinessHours', true);

                        // Reset day cell rendering
                        calendar.setOption('dayCellDidMount', function() {});
                    }
                } else {
                    // Reset to default view when no doctor is filtered
                    document.getElementById('calendar').classList.remove('filtered-view');

                    // Default business hours
                    calendar.setOption('businessHours', [{
                        daysOfWeek: [1, 2, 3, 4, 5],
                        startTime: '09:00',
                        endTime: '17:00'
                    }]);

                    // Remove constraints when no doctor is selected
                    calendar.setOption('selectConstraint', null);
                    calendar.setOption('displayNonBusinessHours', true);

                    // Reset day cell rendering
                    calendar.setOption('dayCellDidMount', function() {});
                }

                // Redraw the calendar to apply changes
                calendar.render();

                // Apply random colors after filter changes
                // applyRandomColors();
                applyDoctorColors();
            });


            // Close modal after actions
            Livewire.on('closeModal', () => {
                if (modal) {
                    modal.hide();
                    if ($(".selectize-service")[0]?.selectize) {
                        $(".selectize-service")[0].selectize.destroy();
                    }
                }
            });

            function initializePageSelectize() {
                // Doctor filter selectize
                $(".selectize-doctor-filter").selectize({
                    delimiter: ",",
                    persist: false,
                    plugins: ["remove_button", "clear_button"],
                    onChange: function(value) {
                    @this.set('filter_doctor_id', value)
                    @this.loadDoctorAvailability()
                    }
                });
            }

            Livewire.on('disableSelectizeFields', () => {
                if ($(".selectize-doctor")[0]?.selectize) {
                    $(".selectize-doctor")[0].selectize.disable();
                }
                if ($(".selectize-patient")[0]?.selectize) {
                    $(".selectize-patient")[0].selectize.disable();
                }
            });

            // Add this listener after your other Livewire listeners
            Livewire.on('serviceSelectizeUpdate', (data) => {
                const $el = $(".selectize-service");

                if ($el.length) {
                    if ($el[0].selectize) {
                        $el[0].selectize.destroy();
                    }

                    $el.selectize({
                        delimiter: ",",
                        persist: true,
                        plugins: ["remove_button", "clear_button"],
                        maxItems: null,
                        create: false,
                        valueField: 'id',
                        labelField: 'title',
                        searchField: 'title',
                        onChange: function(value) {
                        @this.set('service_ids', value);
                        },
                        onItemAdd: function(value, $item) {
                        @this.set('service_ids', this.items);
                        },
                        onItemRemove: function(value) {
                        @this.call('removeServiceById', value);
                        }
                    });

                    // Refill options and selected
                    const selectize = $el[0].selectize;
                    const options = data.values || [];
                    const selected = data.selected || [];

                    options.forEach(opt => {
                        selectize.addOption({ id: opt.id, title: `${opt.description} $${opt.price}` });
                    });

                    selected.forEach(id => {
                        selectize.addItem(id);
                    });
                }
            });


            // Also update the part that reinitializes selectize
            Livewire.on('initializeSelectize', () => {
                if ($(".selectize-doctor")[0] && $(".selectize-doctor")[0].selectize) {
                    $(".selectize-doctor")[0].selectize.destroy();
                }
                if ($(".selectize-patient")[0] && $(".selectize-patient")[0].selectize) {
                    $(".selectize-patient")[0].selectize.destroy();
                }
                // if ($(".selectize-service")[0]?.selectize) {
                //     $(".selectize-service")[0].selectize.setValue(@this.service_ids);
                //     // $(".selectize-service")[0].selectize.clear(true);

                // }

                // Form selectize fields
                // Initialize doctor selectize
                $(".selectize-doctor").selectize({
                    delimiter: ",",
                    persist: false,
                    plugins: ["remove_button", "clear_button"],
                    onChange: function(value) {
                    @this.set('doctor_id', value)
                    },
                    onInitialize: function() {
                        if (@this.event_id) {
                            this.disable();
                        } else {
                            this.enable();
                            this.clear(); // ensure it's clean when adding new
                        }
                    }
                });
                $(".selectize-doctor")[0].selectize.clear(true);

                // Initialize patient selectize
                $(".selectize-patient").selectize({
                    delimiter: ",",
                    persist: false,
                    plugins: ["remove_button", "clear_button"],
                    onChange: function(value) {
                    @this.set('patient_id', value);
                    },
                    create: false,
                    onType: function(search) {
                        // Save to Livewire variable
                    @this.set('searchTerm', search);
                    },
                    onInitialize: function() {
                        if (@this.event_id) {
                            this.disable();
                        } else {
                            this.enable();
                            this.clear();
                        }
                    },
                    render: {
                        no_results: function(data, escape) {
                            return `<div class="no-results text-center py-2">
            <span>No patient found</span>
            <br>
            <button type="button" class="btn btn-sm btn-primary mt-1" onclick="window.livewire.emit('showAddPatient')">
                Add Patient
            </button>
        </div>`;
                        }
                    }
                });
                $(".selectize-patient")[0].selectize.clear(true);

                if (@this.doctor_id) {
                    $(".selectize-doctor")[0].selectize.setValue(@this.doctor_id);
                }
                if (@this.patient_id) {
                    $(".selectize-patient")[0].selectize.setValue(@this.patient_id);
                }
                // if (@this.service_ids && @this.service_ids.length) {
                //     $(".selectize-service")[0].selectize.setValue(@this.service_ids);
                // }
            });

            Livewire.on('reset-doctor-filter', () => {
                // If using Selectize
                if ($('#filter_doctor_id')[0].selectize) {
                    $('#filter_doctor_id')[0].selectize.setValue('', false);
                }
            });

            Livewire.on('markUnavailableDatesBatch', (data) => {
                const allCells = document.querySelectorAll('.fc-daygrid-day');

                allCells.forEach(cell => {
                    const date = cell.getAttribute('data-date');
                    cell.classList.remove('fc-day-unavailable', 'fc-day-nonworking');

                    if (data.unavailable.includes(date)) {
                        cell.classList.add('fc-day-unavailable');
                    }
                    if (data.nonworking.includes(date)) {
                        cell.classList.add('fc-day-nonworking');
                    }
                });
            });

        });
    </script>
@endpush
