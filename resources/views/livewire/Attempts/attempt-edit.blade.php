@push('style')
    <style>
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
            white-space: nowrap;
        }

        .selectize-control.multi .selectize-input > div img {
            width: 20px;
            height: 20px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 5px;
        }

        .selectize-control .selectize-dropdown .option {
            display: flex;
            align-items: center;
            padding: 6px 10px;
        }

        .selectize-control .selectize-dropdown .option img {
            width: 28px;
            height: 28px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 8px;
        }

        [x-cloak] {
            display: none !important;
        }

        svg {
            pointer-events: none;
        }

        /* Fixed spacing for form columns */
        .form-column {
            margin-bottom: 1rem;
        }

        /* Star rating hover effects */
        .star-container {
            transition: transform 0.1s ease;
        }

        .star-container:hover {
            transform: scale(1.1);
        }

        .star-half:hover,
        .star-full:hover {
            opacity: 0.8;
        }

        .custom-switch:checked {
            background-color: #fa8072 !important; /* Light green */
        }
        .custom-switch {
            background-color: #f43d2b !important; /* Default color */
        }


    </style>
@endpush

<div class="container py-4">
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('message') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible text-white" role="alert">
            <span class="text-sm">
                <strong>Oops!</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow-lg">
        <div class="card-header d-flex justify-content-between align-items-center">
            Edit Attempt
        </div>

        <div class="card-body">
            <div
                x-data="{ successful: @entangle('successful'), hoverRating: null, rating: {{ floatval($satisfaction_rating ?? 0) }} }">

                <div class="row mb-3 align-items-end">
                    <!-- Successful? (Always left) -->
                    <div class="col-md-3 form-column">
                        <h4 class="text-secondary">Successful?</h4>
                        <div class="form-check form-switch form-check-lg">
                            <input type="checkbox"
                                   class="form-check-input custom-switch"
                                   style="transform: scale(1.5);"
                                   id="successful"
                                   wire:model="successful"
                                      @if($successful)
                                        checked
                                        @endif
                                   role="switch">
                        </div>
                        @error('successful')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>


                    <!-- Reason (if unsuccessful) -->
                    <div class="col-md-6 form-column" x-show="!successful" x-cloak>
                        <label for="attempt_reason" class="form-label">Reason</label>
                        <input type="text" id="attempt_reason" wire:model="attempt_reason"
                               class="form-control border border-2 p-2">
                        @error('attempt_reason')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <!-- Duration (if successful) -->
                    <div class="col-md-6 form-column" x-show="successful" x-cloak>
                        <label for="duration_minutes" class="form-label">Duration</label>
                        <input type="number" min="0" id="duration_minutes" wire:model="duration_minutes"
                               placeholder="Enter duration in minutes"
                               class="form-control border border-2 p-2">
                        @error('duration_minutes')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>


                    <div class="col-md-3 form-column ms-auto text-end" x-show="successful" x-cloak>
                        <h4 class="text-secondary">Lubrication Used?</h4>
                        <div class="form-check form-switch d-flex justify-content-end">
                            <input type="checkbox"
                                   class="form-check-input custom-switch"
                                   style="transform: scale(1.5);"
                                   id="lubrication_used"
                                   wire:model="lubrication_used"
                                   @if($lubrication_used)
                                       checked
                                   @endif
                                   role="switch">
                        </div>
                        @error('lubrication_used')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="attempt_date">Attempt Date *</label>
                        <input type="date" id="attempt_date" wire:model="attempt_date"
                               class="form-control border border-2 p-2">
                        @error('attempt_date')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="attempt_time">Attempt Time</label>
                        <input type="time" id="attempt_time" wire:model="attempt_time"
                               class="form-control border border-2 p-2">
                        @error('attempt_time')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="attempt_type">Attempt Type</label>
                        <select id="attempt_type" wire:model="attempt_type"
                                class="form-control border border-2 p-2">
                            <option value="">Select type...</option>
                            <option value="intercourse">Intercourse</option>
                            <option value="intimacy">Intimacy</option>
                            <option value="foreplay">Foreplay</option>
                            <option value="other">Other</option>
                        </select>
                        @error('attempt_type')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-6" wire:ignore>
                        <label>Intimacy</label>
                        <select class="selectize_positions" multiple>
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


                <div x-show="successful" x-cloak>
                    <div class="d-flex justify-content-center my-4">
                        <div class="d-flex align-items-center gap-1" x-data="{
                            rating: rating,
                            hoverRating: hoverRating,
                            setRating(value) {
                                rating = value;
                                $wire.satisfaction_rating = value;
                            },
                            getFill(index) {
                                const r = hoverRating ?? rating;
                                if (r >= index) return 'full';
                                if (r >= index - 0.5) return 'half';
                                return 'empty';
                            },
                            onMouseEnter(value) {
                                hoverRating = value;
                            },
                            onMouseLeave() {
                                hoverRating = null;
                            }
                        }">
                            <template x-for="i in [1,2,3,4,5]" :key="i">
                                <div class="position-relative star-container mt-3"
                                     style="width: 70px; height: 70px; cursor: pointer;">
                                    <!-- Left Half -->
                                    <div class="position-absolute top-0 start-0 h-100 star-half"
                                         style="width: 50%; z-index: 10;"
                                         @click="setRating(i - 0.5)"
                                         @mouseenter="onMouseEnter(i - 0.5)"
                                         @mouseleave="onMouseLeave()"></div>

                                    <!-- Right Half -->
                                    <div class="position-absolute top-0 end-0 h-100 star-full"
                                         style="width: 50%; z-index: 10;"
                                         @click="setRating(i)"
                                         @mouseenter="onMouseEnter(i)"
                                         @mouseleave="onMouseLeave()"></div>

                                    <!-- Base Star with yellow border when selected or hovered -->
                                    <svg class="position-absolute top-0 start-0 w-100 h-100" fill="none"
                                         :stroke="hoverRating ? '#facc15' : (rating >= i - 0.5 ? '#facc15' : '#ccc')"
                                         stroke-width="1.5" viewBox="0 0 24 24">
                                        <path
                                            d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z"/>
                                    </svg>

                                    <!-- Full Fill Star -->
                                    <template x-if="getFill(i) === 'full'">
                                        <svg class="position-absolute top-0 start-0 w-100 h-100"
                                             fill="#facc15" :stroke="'#facc15'" stroke-width="1.5"
                                             viewBox="0 0 24 24">
                                            <path
                                                d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z"/>
                                        </svg>
                                    </template>

                                    <!-- Half Fill Star -->
                                    <template x-if="getFill(i) === 'half'">
                                        <svg class="position-absolute top-0 start-0 w-100 h-100"
                                             :stroke="'#facc15'" stroke-width="1.5"
                                             viewBox="0 0 24 24">
                                            <defs>
                                                <linearGradient :id="'half-grad-' + i" x1="0" x2="100%" y1="0" y2="0">
                                                    <stop offset="50%" stop-color="#facc15"/>
                                                    <stop offset="50%" stop-color="transparent"/>
                                                </linearGradient>
                                            </defs>
                                            <path
                                                :fill="'url(#half-grad-' + i + ')'"
                                                d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z"/>
                                        </svg>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label for="description">Description</label>
                        <textarea id="description" wire:model="description" rows="4"
                                  class="form-control border border-2 p-2"
                                  placeholder="Additional details..."></textarea>
                        @error('description')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" wire:click="update" wire:loading.attr="disabled"
                            class="btn bg-gradient-dark btn-md mt-4 mb-4">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        document.addEventListener('livewire:init', () => {
            const selectizeInstances = {};

            function initializeSelectize({
                                             selector,
                                             livewireEvent,
                                             valueField = 'id',
                                             labelField = 'name',
                                             searchField = 'name',
                                             options = [],
                                             selected = null,
                                             clearOption = false
                                         }) {
                let $select = $(selector);
                if ($select[0]?.selectize) {
                    $select[0].selectize.destroy();
                }
                $select.selectize({
                    persist: false,
                    plugins: ['remove_button', 'clear_button'],
                    valueField,
                    labelField,
                    searchField,
                    create: false,
                    placeholder: 'Select intimacy...',
                    onChange: function (values) {
                        Livewire.dispatch(livewireEvent, [values]);
                    },
                    render: {
                        option: function (item, escape) {
                            return `<div class="d-flex align-items-center">
                            <img src="${escape(item.photo)}" alt="photo" style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                            <span>${escape(item.name)}</span>
                        </div>`;
                        },
                        item: function (item, escape) {
                            return `<div class="d-flex align-items-center">
                            <img src="${escape(item.photo)}" alt="photo" style="width: 20px; height: 20px; object-fit: cover; border-radius: 3px; margin-right: 6px;">
                            <span>${escape(item.name)}</span>
                        </div>`;
                        }
                    },
                    onInitialize: function () {
                        selectizeInstances[selector] = this;
                        if (clearOption) {
                            this.clear();
                            this.clearOptions();
                        }
                        if (options && options.length) {
                            options.forEach(option => {
                                this.addOption(option);
                            });
                        }
                        this.refreshOptions(false);
                        if (selected && selected.length) {
                            this.setValue(selected);
                        }
                    }
                });
            }

            initializeSelectize({
                selector: '.selectize_positions',
                livewireEvent: 'positionSelectize',
                options: @json($positionOptions),
                selected: @json($selectedPositions)
            });
        });
    </script>
@endpush
