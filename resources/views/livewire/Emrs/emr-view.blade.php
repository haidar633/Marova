<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">

                    @php
                        $userPreferences = auth()->user()->preferences()->first();
                        $activeColor = $userPreferences ? $userPreferences->sidebar_color : 'primary';
                    @endphp
                    <div class="bg-gradient-{{ $activeColor }} shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white mx-3"><strong> Add, Edit, Delete Emrs</strong> </h6>
                    </div>
                </div>
                <div class="me-3 my-3 text-end">
                    <a class="btn bg-gradient-dark mb-0" href="{{ route('add-emr') }}">
                        <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Add New Emr
                    </a>
                </div>

                <div class="row">
                    <div class="col-md-3 px-5">
                        <input type="search" wire:model.live="search" class="form-control border border-2 p-2"
                               placeholder="{{ __('Search') }} by patient or title">
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                            <tr>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Patient
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    TITLE
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Actions
                                </th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($emrs as $key => $u)
                                <tr>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-s font-weight-bold">
                                            {{ $u->patient->fname ?? null }}
                                            {{ $u->patient->lname ?? null }}
                                        </span>
                                    </td>

                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-s font-weight-bold">
                                            {{ $u->title ??null }}
                                        </span>
                                    </td>

                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-s font-weight-bold">
                                            @can('emr-edit')
                                                <a rel="tooltip" class="btn btn-success btn-link"
                                                   href="{{ route('edit-emr', $u->id) }}" title="">
                                                    <i class="material-icons">edit</i>
                                                </a>
                                            @endcan
                                            @can('emr-delete')
                                                <button type="button" class="btn btn-danger btn-link"
                                                        title="" onclick="confirmDelete({{ $u->id }})">
                                                    <i class="material-icons">close</i>
                                                </button>
                                            @endcan
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="px-4 py-3">
                            {{ $emrs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
