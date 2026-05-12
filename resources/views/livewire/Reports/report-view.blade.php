<div class="container mt-4">
    <div class="row">
        <div class="col-12 col-md-3 order-1">
            <div class="card text-center shadow mb-3">
                <div class="card-body">
                    <h6>Center Share</h6>
                    <h3>${{ number_format($centerShareTotal, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3 order-2">
            <div class="card text-center shadow mb-3">
                <div class="card-body">
                    <h6>Doctor Share</h6>
                    <h3>${{ number_format($doctorShareTotal, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3 order-3">
            <div class="card text-center shadow mb-3">
                <div class="card-body">
                    <h6>Patients</h6>
                    <h3>{{ $nb_of_patients }}</h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3 order-4">
            <div class="card text-center shadow mb-3">
                <div class="card-body">
                    <h6>Appointments</h6>
                    <h3>{{ $nb_of_appointments }}</h3>
                </div>
            </div>
        </div>
    </div>




    <!-- Date Filters -->
    <div class="row mt-3">
        <div class="col-md-3">
            <label for="start_date">From</label>
            <input type="date" id="start_date" wire:model.live="start_date"
                   class="form-control border border-2 p-2 @error('start_date') is-invalid @enderror">
            @error('start_date') <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-3">
            <label for="end_date">To</label>
            <input type="date" id="end_date" wire:model.live="end_date"
                   class="form-control border border-2 p-2 @error('end_date') is-invalid @enderror">
            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-3 mb-3">
            <label for="doctor_id" class="form-label">Doctor</label>
            <div class="@error('doctor_id') is-invalid @enderror">
                <div wire:ignore>
                    <select class="selectize-doctor" id="doctor_id">
                        <option value="">Select Doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">
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

        <div class="col-md-3 mt-4">
            <button wire:click="applyFilters" class="btn btn-primary">
                Apply Filter
            </button>
        </div>
    </div>

    <!-- Report Data Display -->
    @if(count($reportData))
        <div class="row mt-3">
            <div class="col-12">
                <div class="card border shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Detailed Doctor Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Doctor</th>
                                    <th class="text-end">Appointments</th>
                                    <th class="text-end">Patients</th>
                                    <th class="text-end">Total Billed</th>
                                    <th class="text-end">Total Paid</th>
                                    <th class="text-end">Remaining</th>
                                    <th class="text-end">Doctor Share (%) </th>
                                    <th class="text-end">Center Share (%)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($reportData as $report)
                                    <tr>
                                        <td>{{ $report['doctor_name'] }}</td>
                                        <td class="text-end">{{ $report['appointment_count'] }}</td>
                                        <td class="text-end">{{ $report['patient_count'] }}</td>
                                        <td class="text-end">${{ number_format($report['total'], 2) }}</td>
                                        <td class="text-end">${{ number_format($report['paid'], 2) }}</td>
                                        <td class="text-end" class="{{ $report['remaining'] > 0 ? 'text-danger' : 'text-success' }}">
                                            ${{ number_format($report['remaining'], 2) }}
                                        </td>
                                        <td class="text-end">${{ number_format($report['doctor_price'], 2) }} ({{ $report['doctor_percentage'] }}%)</td>
                                        <td class="text-end">${{ number_format($report['center'], 2) }} ({{ $report['center_percentage'] }}%)</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr class="table-light">
                                    <th>Totals</th>
                                    <th class="text-end">{{ $nb_of_appointments }}</th>
                                    <th class="text-end">{{ $nb_of_patients }}</th>
                                    <th class="text-end">${{ number_format($totalBilledAmount, 2) }}</th>
                                    <th class="text-end">${{ number_format($totalPaidAmount, 2) }}</th>
                                    <th class="text-end" class="{{ $totalRemainingAmount > 0 ? 'text-danger' : 'text-success' }}">
                                        ${{ number_format($totalRemainingAmount, 2) }}
                                    </th>
                                    <th class="text-end">${{ number_format($doctorShareTotal, 2) }}</th>
                                    <th class="text-end">${{ number_format($centerShareTotal, 2) }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

{{--        <div class="row mt-3">--}}
{{--            <div class="col-12 text-end">--}}
{{--                <button wire:click="exportPDF" class="btn btn-primary">--}}
{{--                    <i class="fa fa-download me-2"></i> Export Report as PDF--}}
{{--                </button>--}}
{{--            </div>--}}
{{--        </div>--}}
    @else
        <div class="alert alert-info text-center text-white mt-3">
            No data found for the selected date range.
        </div>
    @endif
</div>

@push('js')
    <script>
        document.addEventListener('livewire:init', () => {
            const initializeSelectize = () => {
                $(".selectize-patient").selectize({
                    delimiter: ",",
                    persist: false,
                    plugins: ["remove_button", "clear_button"],
                    valueField: 'id',
                    labelField: 'name',
                    searchField: ['name'],
                    create: false,
                    onChange: function (value) {
                    @this.set('patient_id', value)

                    },
                    onClear: function () {
                    @this.set('patient_id', '')

                    }
                });

                $(".selectize-doctor").selectize({
                    delimiter: ",",
                    persist: false,
                    plugins: ["remove_button", "clear_button"],
                    valueField: 'id',
                    labelField: 'name',
                    searchField: ['name'],
                    create: false,
                    onChange: function (value) {
                    @this.set('doctor_id', value)

                    },
                    onClear: function () {
                    @this.set('doctor_id', '')

                    }
                });
            };

            initializeSelectize();

            Livewire.on('saved', () => {
                const selectizePatient = $(".selectize-patient")[0]?.selectize;
                if (selectizePatient) {
                    selectizePatient.clear();
                }

                const selectizeDoctor = $(".selectize-doctor")[0]?.selectize;
                if (selectizeDoctor) {
                    selectizeDoctor.clear();
                }
            });

            Livewire.on('scrollToElement', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
@endpush
