<div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('success') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('error') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    <div class="container">
        <div class="justify-content-center">
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
                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"
                            aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            @endif

            {{-- Removed overlay/loader for brevity, can be added back if needed --}}
            {{-- <div class="overlay">
                <div class="loader"></div>
            </div> --}}
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">Edit Patient
                    <span class="float-right"> {{-- Removed mt-3 as it was on span inside span --}}
                        <span class="d-flex align-items-center">
                            <a class="btn btn-primary" href="{{ route('patients') }}">Patients</a>
                        </span>
                    </span>
                </div>
                <div class="card-body">
                    <!-- Basic Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{-- <div class="form-group col-12"> --}} {{-- Redundant inner div --}}
                                <label for="fname">First Name</label>
                                <input wire:model.live="fname" type="text"
                                       class="form-control border border-2 p-2" id="fname"
                                       placeholder="Enter first name">
                                {{-- </div> --}}
                                @error('fname')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{-- <div class="form-group col-12"> --}}
                                <label for="lname">Last Name</label>
                                <input wire:model.live="lname" type="text"
                                       class="form-control border border-2 p-2" id="lname"
                                       placeholder="Enter last name">
                                {{-- </div> --}}
                                @error('lname')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{-- <div class="form-group col-12"> --}}
                                <label for="date_of_birth">Date Of Birth</label>
                                <input wire:model.live="date_of_birth" type="date"
                                       class="form-control border border-2 p-2" id="date_of_birth">
                                {{-- </div> --}}
                                @error('date_of_birth')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="gender">Gender</label>
                            <select id="gender" wire:model.live="gender" class="form-select border border-2 p-2">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            @error('gender')
                            <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6 mt-3"> {{-- Changed to col-md-6 for consistency --}}
                            <label for="email">Email</label>
                            <input wire:model="email" type="email"
                                   class="form-control border border-2 p-2" id="email">
                            @error('email')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 mt-3"> {{-- Changed to col-md-6 --}}
                            <label for="serial_nb">Serial Number</label>
                            <input wire:model="serial_nb" type="text"
                                   class="form-control border border-2 p-2" id="serial_nb" readonly>
                            @error('serial_nb')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="col-md-12">
                            <label for="medications">Address</label>
                            <textarea wire:model="address" class="form-control border border-2 p-2" rows="2"></textarea>
                            @error('address')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <!-- Contact Information Section -->
                    <div class="mt-4">
                        <h2>Contact Info</h2>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <button class="btn btn-success w-100" wire:click.prevent="addContactInfo">
                                    Add Contact Info
                                </button>
                            </div>
                        </div>

                        @foreach($contactInfos as $index => $info)
                            <div class="row mt-3 align-items-end">
                                <div class="col-md-3">
                                    <select wire:model.live="contactInfos.{{ $index }}.type"
                                            class="form-select border border-2 p-2">
                                        <option value="">Select Type</option>
                                        <option value="phone">Phone</option>
                                        <option value="whatsapp">WhatsApp</option>
                                    </select>
                                    @error("contactInfos.{$index}.type")
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(isset($info['type']) && $info['type'])
                                    <div class="col-md-7">
                                        <div class="form-group mb-0">
                                            <label class="mb-2">
                                                @if($info['type'] === 'phone')
                                                    Phone {{ $this->getPhoneLabel($index) }}
                                                @else
                                                    {{ ucfirst($info['type']) }}
                                                @endif
                                            </label>
                                            <input wire:model.live="contactInfos.{{ $index }}.value"
                                                   type="text"
                                                   class="form-control border border-2 p-2"
                                                   placeholder="Enter {{ $info['type'] }}">
                                        </div>
                                        @error("contactInfos.{$index}.value")
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif

                                <div class="col-md-2">
                                    {{-- Allow removing the first item if there's more than one, or if it's the only one and empty --}}
                                    @if(count($contactInfos) > 1 || (isset($contactInfos[$index]['id']) && $contactInfos[$index]['id']))
                                        <button class="btn btn-danger w-100 mb-1"
                                                wire:click.prevent="removeContactInfo({{ $index }})">
                                            Remove
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row mt-6">
                        <h2>Patient Details</h2>

                        <ul class="nav nav-tabs mt-3" id="medicalTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link @if($activeTab === 'medical') active @endif"
                                        wire:click="setActiveTab('medical')"
                                        type="button">
                                    Medical History
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link @if($activeTab === 'additional') active @endif"
                                        wire:click="setActiveTab('additional')"
                                        type="button">
                                    Past Medical History
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link @if($activeTab === 'family') active @endif"
                                        wire:click="setActiveTab('family')"
                                        type="button">
                                    Family History
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link @if($activeTab === 'social') active @endif"
                                        wire:click="setActiveTab('social')"
                                        type="button">
                                    Social History
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content p-3 border border-top-0" id="medicalTabsContent">
                            {{-- Medical History Tab --}}
                            <div class="tab-pane fade @if($activeTab === 'medical') show active @endif">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="medications">Medications & Dosage</label>
                                        <textarea wire:model="medications.medication_dosage" class="form-control border border-2 p-2" rows="2"></textarea>
                                        @error('medications.medication_dosage')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="allergies">Allergies</label>
                                        <textarea wire:model="allergies" class="form-control border border-2 p-2" rows="2"></textarea>
                                        @error('allergies')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label for="chronic_disease">Chronic Disease</label>
                                        <textarea wire:model="chronic_disease" class="form-control border border-2 p-2" rows="2"></textarea>
                                        @error('chronic_disease')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="chronic_medications">Chronic Medications</label>
                                        <textarea wire:model="medications.chronic" class="form-control border border-2 p-2" rows="2"
                                                  placeholder="e.g., Metformin for diabetes, Lisinopril for hypertension"></textarea>
                                        @error('medications.chronic')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label for="acute_medications">Current/Acute Medications</label>
                                        <textarea wire:model="medications.acute" class="form-control border border-2 p-2" rows="2"
                                                  placeholder="e.g., Antibiotics for infection, Painkillers for recent injury"></textarea>
                                        @error('medications.acute')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="vitamins">Vitamins & Supplements</label>
                                        <textarea wire:model="medications.vitamins" class="form-control border border-2 p-2" rows="2"
                                                  placeholder="e.g., Multivitamin, Vitamin D, Iron, Omega-3"></textarea>
                                        @error('medications.vitamins')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label for="herbals">Herbal or Alternative Medications</label>
                                        <textarea wire:model="medications.herbal" class="form-control border border-2 p-2" rows="2"
                                                  placeholder="e.g., Ginseng, St. John's Wort, Chamomile"></textarea>
                                        @error('medications.herbal')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>



                            <div class="tab-pane fade @if($activeTab === 'additional') show active @endif">
                                <p class="text-muted">Please check any condition that applies or add details where
                                    necessary.</p>

                                {{-- Cardiovascular --}}
                                <div class="mb-3">
                                    <strong class="fs-4">1. Cardiovascular:</strong><br>
                                    @foreach(['Hypertension', 'Heart attack (MI)', 'Arrhythmia', 'Congestive heart failure', 'Hyperlipidemia'] as $item)
                                        <div class="form-check border border-0 p-2">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="medicalHistory.cardiovascular.{{ $item }}"
                                                   id="cardio-{{ Str::slug($item) }}">
                                            <label class="form-check-label"
                                                   for="cardio-{{ Str::slug($item) }}">{{ $item }}</label>
                                        </div>
                                    @endforeach
                                </div>


                                {{-- Respiratory --}}
                                <div class="mb-3">
                                    <strong class="fs-4">2. Respiratory:</strong><br>
                                    @foreach(['Asthma', 'COPD', 'Tuberculosis', 'Sleep Apnea'] as $item)
                                        <div class="form-check border border-0 p-2">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="medicalHistory.respiratory.{{ $item }}"
                                                   id="resp-{{ Str::slug($item) }}">
                                            <label class="form-check-label"
                                                   for="resp-{{ Str::slug($item) }}">{{ $item }}</label>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Neurological --}}
                                <div class="mb-3">
                                    <strong class="fs-4">3. Neurological:</strong><br>
                                    @foreach(['Stroke / TIA', 'Epilepsy / Seizures', 'Multiple sclerosis', 'Parkinson’s disease', 'Migraines', 'Neuropathy'] as $item)
                                        <div class="form-check border border-0 p-2">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="medicalHistory.neuro.{{ $item }}"
                                                   id="neuro-{{ Str::slug($item) }}">
                                            <label class="form-check-label"
                                                   for="neuro-{{ Str::slug($item) }}">{{ $item }}</label>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Endocrine --}}
                                <div class="mb-3">
                                    <strong class="fs-4">4. Endocrine:</strong><br>
                                    @foreach(['Diabetes (Type 1 / Type 2)', 'Thyroid disorder (Hypo / Hyper)', 'Adrenal insufficiency', 'PCOS'] as $item)
                                        <div class="form-check border border-0 p-2">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="medicalHistory.endocrine.{{ $item }}"
                                                   id="endo-{{ Str::slug($item) }}">
                                            <label class="form-check-label"
                                                   for="endo-{{ Str::slug($item) }}">{{ $item }}</label>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- GI --}}
                                <div class="mb-3">
                                    <strong class="fs-4">5. Gastrointestinal:</strong><br>
                                    @foreach(['GERD', 'Peptic Ulcer', 'Hepatitis (A/B/C)', "IBD (Crohn's/UC)"] as $item)
                                        <div class="form-check border border-0 p-2">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="medicalHistory.gi.{{ $item }}"
                                                   id="gi-{{ Str::slug($item) }}">
                                            <label class="form-check-label"
                                                   for="gi-{{ Str::slug($item) }}">{{ $item }}</label>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- GU --}}
                                <div class="mb-3">
                                    <strong class="fs-4">6. Genitourinary:</strong><br>
                                    @foreach(['Kidney stones', 'Urinary tract infections (chronic)', 'Prostate issues', 'Menstrual irregularities'] as $item)
                                        <div class="form-check border border-0 p-2">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="medicalHistory.gu.{{ $item }}"
                                                   id="gu-{{ Str::slug($item) }}">
                                            <label class="form-check-label"
                                                   for="gu-{{ Str::slug($item) }}">{{ $item }}</label>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- MSK --}}
                                <div class="mb-3">
                                    <strong class="fs-4">7. Musculoskeletal:</strong><br>
                                    @foreach(['Osteoarthritis', 'Rheumatoid arthritis', 'Osteoporosis', 'Gout'] as $item)
                                        <div class="form-check border border-0 p-2">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="medicalHistory.ms.{{ $item }}"
                                                   id="ms-{{ Str::slug($item) }}">
                                            <label class="form-check-label"
                                                   for="ms-{{ Str::slug($item) }}">{{ $item }}</label>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Mental --}}
                                <div class="mb-3">
                                    <strong class="fs-4">8. Mental Health:</strong><br>
                                    @foreach(['Depression', 'Anxiety', 'Bipolar disorder', 'Schizophrenia'] as $item)
                                        <div class="form-check border border-0 p-2">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="medicalHistory.mental.{{ $item }}"
                                                   id="mental-{{ Str::slug($item) }}">
                                            <label class="form-check-label"
                                                   for="mental-{{ Str::slug($item) }}">{{ $item }}</label>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Cancer --}}
                                <div class="mb-3 col-md-6">
                                    <strong class="fs-4">9. Cancer:</strong><br>
                                    <label for="cancer_type">Type(s):</label>
                                    <input wire:model="medicalHistory.cancer_type" type="text"
                                           class="form-control border border-2 p-2" id="lname"
                                           placeholder="Enter type of cancer">
                                    <label for="cancer_date">Date of Diagnosis:</label>
                                    <input wire:model="medicalHistory.cancer_date" type="date"
                                           class="form-control border border-2 p-2" id="date_of_birth">
                                </div>


                                {{-- Other Notes --}}
                                <div class="mb-3">
                                    <strong class="fs-4">10. Other Chronic Illnesses or Surgeries:</strong>
                                    <textarea wire:model="medicalHistory.other_notes"
                                              class="form-control border border-2 p-2" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="tab-pane fade @if($activeTab === 'family') show active @endif">
                                <p class="text-muted">Check conditions that have affected any first-degree relative
                                    (parents, siblings, children). Specify who and age at diagnosis if known.</p>
                                @foreach($familyConditions as $index => $condition)
                                    <div class="row align-items-center mb-3">
                                        <div class="col-md-4 fs-5">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       wire:model="familyHistory.{{ $index }}.checked"
                                                       id="family-{{ Str::slug($condition) }}">
                                                <label class="form-check-label"
                                                       for="family-{{ Str::slug($condition) }}">{{ $condition }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control border border-2 p-2"
                                                   wire:model="familyHistory.{{ $index }}.details"
                                                {{--                                                   placeholder="Relative(s) affected & details"--}}
                                            >
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Social History Tab --}}
                            <div class="tab-pane fade @if($activeTab === 'social') show active @endif">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Smoking Status</label>
                                        <select class="form-select border border-2 p-2"
                                                wire:model="socialHistory.smoking_status">
                                            <option value="">Select</option>
                                            <option value="Smoker">Smoker</option>
                                            <option value="Non-Smoker">Non-Smoker</option>
                                            <option value="Former Smoker">Former Smoker</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Argileh Status</label>
                                        <select class="form-select border border-2 p-2"
                                                wire:model="socialHistory.argileh_status">
                                            <option value="">Select</option>
                                            <option value="Smoker">Smoker</option>
                                            <option value="Non-Smoker">Non-Smoker</option>
                                            <option value="Former Smoker">Former Smoker</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Alcohol Consumption</label>
                                        <select class="form-select border border-2 p-2"
                                                wire:model.live="socialHistory.alcohol">
                                            <option value="">Select</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>

                                    @if($socialHistory['alcohol'] === 'Yes')
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">If yes, frequency</label>
                                            <select class="form-select border border-2 p-2"
                                                    wire:model.live="socialHistory.frequency">
                                                <option value="">Select</option>
                                                <option value="Occasionally">Occasionally</option>
                                                <option value="Weekly">Weekly</option>
                                                <option value="Daily">Daily</option>
                                                <option value="Other">Other (please specify)</option>
                                            </select>
                                        </div>
                                    @endif
                                </div>

                                <div class="row">
                                    @if($socialHistory['alcohol'] === 'Yes' && $socialHistory['frequency'] === 'Other')
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Specify other frequency</label>
                                            <input type="text" class="form-control border border-2 p-2"
                                                   wire:model="socialHistory.other_frequency">
                                        </div>
                                    @endif

                                    @if($socialHistory['alcohol'] === 'Yes')
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">How many glasses?</label>
                                            <input type="number" class="form-control border border-2 p-2"
                                                   wire:model="socialHistory.glasses">
                                        </div>
                                    @endif
                                </div>
                            </div>


                        </div>
                    </div>




                    <div class="d-flex justify-content-end">
                        <button type="button" wire:click="update" wire:loading.attr="disabled" wire:target="update"
                                class="btn bg-gradient-dark btn-md mt-4 mb-4">
                            <span wire:loading.remove wire:target="update">Save Changes</span>
                            <span wire:loading wire:target="update">Saving...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
