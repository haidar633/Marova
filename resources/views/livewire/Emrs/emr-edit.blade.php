<div>
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

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card card-frame">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        {{ $viewOnly ? 'View' : 'Edit' }} Electronic Medical Record (EMR)
                        <span class="float-right mt-3">
                            @if(!$viewOnly)
                                <span class="d-flex align-items-center">
                                    <a class="btn btn-primary" href="{{ route('emrs') }}">Emrs</a>
                                </span>
                            @endif
                        </span>
                    </div>

                    <div class="card-body">
                        <form wire:submit.prevent="update">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="patient_id" class="form-label">Patient *</label>
                                    <div wire:ignore>
                                        <select class="selectize-patient form-control" id="patient_id"
                                                wire:model.live="patient_id" disabled>
                                            <option value="">Select Patient</option>
                                            @foreach ($patients as $patient)
                                                <option value="{{ $patient['id'] }}"
                                                        @if($patient_id == $patient['id']) selected @endif>
                                                    {{ $patient['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('patient_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="title" class="form-label">EMR Title *</label>
                                    <input wire:model.live="title" type="text"
                                           class="form-control border border-2 p-2 @error('title') is-invalid @enderror"
                                           id="title"
                                           placeholder="Enter EMR title (e.g., MRI Scan)"
                                           @if($viewOnly) disabled @endif>
                                    @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Documents</h5>
                                    @if(!$viewOnly)
                                        <button class="btn btn-success" wire:click.prevent="addEmrInfo">
                                            <i class="fas fa-plus me-1"></i> Add Document
                                        </button>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="input-group">
                                        <input type="text"
                                               wire:model.live="documentSearch"
                                               class="form-control border border-2 p-2"
                                               placeholder="Search documents by description...">
                                    </div>
                                </div>

                                @foreach ($filteredEmrInfos as $index => $info)
                                        <div class="card mb-3 shadow-sm border-0">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Description *</label>
                                                        <input wire:model.live="emrInfos.{{ $index }}.description"
                                                               type="text"
                                                               class="form-control border border-2 p-2 @error("emrInfos.{$index}.description") is-invalid @enderror"
                                                               placeholder="Enter description (e.g., Report, Results)"
                                                               @if($viewOnly) disabled @endif>
                                                        @error("emrInfos.{$index}.description")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">File Upload</label>
                                                        <div class="input-group">
                                                            <input type="file"
                                                                   wire:model="tempFiles.{{ $index }}"
                                                                   class="form-control border border-2 p-2 @error("tempFiles.{$index}") is-invalid @enderror"
                                                                   accept=".jpg,.jpeg,.png,.pdf"
                                                                   @if($viewOnly) disabled @endif>
                                                            <div wire:loading wire:target="tempFiles.{{ $index }}"
                                                                 class="input-group-text">
                                                                <span class="spinner-border spinner-border-sm"
                                                                      role="status"></span>
                                                            </div>
                                                        </div>
                                                        @error("tempFiles.{$index}")
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-4">
                                                        @if(isset($tempFileUrls[$index]) && count($tempFileUrls[$index]) > 0)
                                                            <label class="form-label">Attachment</label>
                                                            <div class="bg-light p-2 rounded d-flex align-items-center justify-content-between w-100">
                                                                <span class="me-2 text-truncate">
                                                                    @php
                                                                        $fileInfo = $tempFileUrls[$index][0];
                                                                        $icon = match(strtolower($fileInfo['extension'])) {
                                                                            'pdf' => 'fa-file-pdf text-danger',
                                                                            'jpg', 'jpeg', 'png' => 'fa-file-image text-info',
                                                                            default => 'fa-file text-secondary'
                                                                        };
                                                                    @endphp
                                                                    <i class="fas {{ $icon }} me-1"></i>
                                                                    {{ Str::limit($fileInfo['filename'], 20) }}
                                                                </span>
                                                                <div>
                                                                    <button wire:click.prevent="downloadFile({{ $index }}, 0)"
                                                                            class="btn btn-outline-primary p-2 me-2">
                                                                        <i class="fas fa-download fa-lg"></i>
                                                                    </button>

                                                                @if(!$viewOnly && $index !== 0)
                                                                        <button wire:click.prevent="removeEmrInfo({{ $index }})"
                                                                                class="btn btn-outline-danger p-2">
                                                                            <i class="fas fa-trash fa-lg"></i>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @elseif(!$viewOnly && $index !== 0)
                                                            <button wire:click.prevent="removeEmrInfo({{ $index }})"
                                                                    class="btn btn-outline-danger p-2 mt-4">
                                                                <i class="fas fa-trash me-1 fa-lg"></i> Remove
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
{{--                                    @endif--}}
                                @endforeach

                                <div class="d-flex justify-content-end">
                                    @if(!$viewOnly)
                                        <button type="submit"
                                                class="btn bg-gradient-dark btn-md mt-4 mb-4">
                                            Save Changes
                                        </button>
                                    @else
                                        <a href="{{ route('patients') }}"
                                           class="btn bg-gradient-primary btn-md mt-4 mb-4">
                                            Back to Patients
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
@push('js')
    <script>
        window.addEventListener('download-file', event => {
            const {url} = event.detail;
            const fullUrl = url.startsWith('http') ? url : window.location.origin + url;
            window.open(fullUrl, '_blank');
        });

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
            };

            initializeSelectize();

            Livewire.on('saved', () => {
                const selectize = $(".selectize-patient")[0]?.selectize;
                if (selectize) selectize.clear();
            });

            setTimeout(() => {
                const selectize = $(".selectize-patient")[0]?.selectize;
                if (selectize) {
                    selectize.setValue(@this.get('patient_id'));
                }
            }, 100);
        });
    </script>
@endpush
