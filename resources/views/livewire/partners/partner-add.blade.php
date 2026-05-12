@push('style')
    <style>
        [x-cloak] {
            display: none !important;
        }

        svg {
            pointer-events: none;
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
    </style>
@endpush

<div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('success') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('error') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="container">
        <div class="justify-content-center">
            @if (count($errors) > 0)
                <div class="alert alert-danger alert-dismissible text-white" role="alert">
                    <span class="text-lg">
                        Oops!
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </span>
                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="overlay">
                <div class="loader"></div>
            </div>
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    Create Partner
                    <span class="float-right mt-3">
                        <span class="d-flex align-items-center">
                            <a class="btn btn-primary" href="{{ route('partners') }}">Back to Partners</a>
                        </span>
                    </span>
                </div>
                <div class="card-body">
                    <div
                        x-data="{ hoverRating: null, rating: {{ $star_rating ? floatval($star_rating) : 0 }} }">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" id="name" wire:model="name"
                                           class="form-control border border-2 p-2" placeholder="Enter partner name (e.g., Sandy)">
                                    @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_day_we_met">Last Day We Met</label>
                                    <input type="date" id="last_day_we_met" wire:model.live="last_day_we_met"
                                           class="form-control border border-2 p-2">
                                    @error('last_day_we_met')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    @if($last_day_we_met)
                                    @php
                                        $now = \Carbon\Carbon::now()->startOfDay();
                                        $lastMet = \Carbon\Carbon::parse($last_day_we_met)->startOfDay();
                                        
                                        // Only calculate if date is in the past
                                        if ($lastMet->isPast() || $lastMet->isToday()) {
                                            $diff = $now->diff($lastMet);
                                            $years = $diff->y;
                                            $months = $diff->m;
                                            $days = $diff->d;
                                            
                                            $parts = [];
                                            if ($years > 0) {
                                                $parts[] = $years . ' year' . ($years > 1 ? 's' : '');
                                            }
                                            if ($months > 0) {
                                                $parts[] = $months . ' month' . ($months > 1 ? 's' : '');
                                            }
                                            if ($days > 0) {
                                                $parts[] = $days . ' day' . ($days > 1 ? 's' : '');
                                            }
                                            
                                            if (empty($parts)) {
                                                $timePassed = 'Today';
                                            } else {
                                                $timePassed = implode(' ', $parts);
                                            }
                                        } else {
                                            $timePassed = null;
                                        }
                                    @endphp
                                    @if($timePassed)
                                        <div class="mt-2">
                                            <span class="text-primary fw-bold" style="font-size: 1.5rem; letter-spacing: 0.5px;">{{ $timePassed }}</span>
                                        </div>
                                    @else
                                        <div class="mt-2">
                                            <span class="text-danger" style="font-size: 1.2rem;">Future date selected</span>
                                        </div>
                                    @endif
                                    @else
                                        <div class="mt-2">
                                            <span class="text-muted">Select a date to see time passed</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image">Image</label>
                                    <input type="file" id="image" class="form-control border border-2 p-2"
                                           wire:model="image">
                                    @error('image')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    @if ($image && $newImageUploaded)
                                        <div class="mt-2 position-relative d-inline-block">
                                            <div class="shadow-sm border-radius-lg overflow-hidden" style="width: 200px; height: 260px; border: 3px solid #fff;">
                                                <img src="{{ $image->temporaryUrl() }}" style="width: 100%; height: 100%; object-fit: cover;"/>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="fw-bold">Star Rating</label>
                                <div class="d-flex justify-content-center my-4">
                                    <div class="d-flex align-items-center gap-1" x-data="{
                                        rating: rating,
                                        hoverRating: hoverRating,
                                        setRating(value) {
                                            rating = value;
                                            $wire.star_rating = value;
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
                                                <!-- Left half click -->
                                                <div class="position-absolute top-0 start-0 h-100 star-half"
                                                     style="width: 50%; z-index: 10;"
                                                     @click="setRating(i - 0.5)"
                                                     @mouseenter="onMouseEnter(i - 0.5)"
                                                     @mouseleave="onMouseLeave()"></div>

                                                <!-- Right half click -->
                                                <div class="position-absolute top-0 end-0 h-100 star-full"
                                                     style="width: 50%; z-index: 10;"
                                                     @click="setRating(i)"
                                                     @mouseenter="onMouseEnter(i)"
                                                     @mouseleave="onMouseLeave()"></div>

                                                <!-- BASE STAR with dynamic border color -->
                                                <svg class="position-absolute top-0 start-0 w-100 h-100" fill="none"
                                                     :stroke="hoverRating ? '#facc15' : (rating >= i - 0.5 ? '#facc15' : '#ccc')"
                                                     stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z"/>
                                                </svg>

                                                <!-- FULL STAR -->
                                                <template x-if="getFill(i) === 'full'">
                                                    <svg class="position-absolute top-0 start-0 w-100 h-100"
                                                         :fill="'#facc15'" :stroke="'#facc15'" stroke-width="1.5"
                                                         viewBox="0 0 24 24">
                                                        <path
                                                            d="M12 .587l3.668 7.431L24 9.75l-6 5.848 1.417 8.268L12 18.902l-7.417 4.964L6 15.598 0 9.75l8.332-1.732z"/>
                                                    </svg>
                                                </template>

                                                <!-- HALF STAR -->
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
                                @error('star_rating')
                                <div class="text-danger text-center">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12 mt-4">
                            <label for="description">Description</label>
                            <textarea wire:model="description" class="form-control border border-2 p-2"
                                      id="description" rows="6" style="text-align: center;" placeholder="Enter description"></textarea>
                            @error('description')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" wire:click="store" wire:loading.attr="disabled"
                                class="btn bg-gradient-dark btn-md mt-4 mb-4">
                            <span wire:loading.remove wire:target="store">Save</span>
                            <span wire:loading wire:target="store">Saving...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
