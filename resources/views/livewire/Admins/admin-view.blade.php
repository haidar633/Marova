<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">

                    @php
                        $userPreferences = auth()->user()->preferences()->first();
                        $activeColor = $userPreferences ? $userPreferences->sidebar_color : 'primary';
                    @endphp
                    <div class="bg-gradient-{{$activeColor}} shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white mx-3"><strong> Add, Edit, Delete Admins</strong> </h6>
                    </div>
                </div>
                <div class=" me-3 my-3 text-end">
                    <a class="btn bg-gradient-dark mb-0" href="{{ route('add-admin') }}"><i
                            class="material-icons text-sm">add</i>&nbsp;&nbsp;Add New Admin</a>
                </div>

                <div class="row">
                    <div class="col-md-3 px-5">
                        <input type="search" wire:model.live="search" class="form-control border border-2 p-2"
                               placeholder="{{ __('Search') }} by serial number or name or email">
                    </div>
                </div>


                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                            <tr>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    SERIAL NUMBER
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    NAME
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    DATE OF BIRTH
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    CONTACTS
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    EMAIL
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    ACTIONS
                                </th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($admins as $key => $u)
                                    <tr>
                                        <td class="align-middle text-center">
                                        <span class="text-secondary text-s font-weight-bold">
                                            {{ $u->serial_nb }}
                                        </span>
                                        </td>

                                        <td class="align-middle text-center">
                                        <span class="text-secondary text-s font-weight-bold">
                                            {{ $u->fname }}
                                            {{ $u->lname }}
                                        </span>
                                        </td>

                                        <td class="align-middle text-center">
                                        <span class="text-secondary text-s font-weight-bold">
                                            {{ $u->date_of_birth }}
                                        </span>
                                        </td>

                                        <td class="align-middle text-center">
                                        <span class="text-secondary text-s font-weight-bold">
                                            @if($u->contactDetail && $u->contactDetail->count() > 0)
                                                @foreach($u->contactDetail as $contact)
                                                    <div class="d-flex align-items-center justify-content-center mb-1">
                                                        @if($contact->type == 'phone')
                                                            <a href="tel:{{ $contact->value }}" class="text-secondary d-flex align-items-center">
                                                                <i class="material-icons text-primary me-1">phone</i>
                                                                <span>{{ preg_replace('/(\d{2})(\d{3})(\d{3})/', '$1 $2 $3', $contact->value) }}</span>
                                                            </a>
                                                        @elseif($contact->type == 'whatsapp')
                                                            <a href="https://wa.me/{{ $contact->value }}" target="_blank" class="text-secondary d-flex align-items-center">
                                                                <i class="fab fa-whatsapp text-success me-1" style="font-size: 20px;"></i>
                                                                <span>{{ preg_replace('/(\d{2})(\d{3})(\d{3})/', '$1 $2 $3', $contact->value) }}</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        </span>
                                        </td>

                                        <td class="align-middle text-center">
                                        <span class="text-secondary text-s font-weight-bold">
                                            <a href="mailto:{{ $u->email }}" class="text-secondary d-flex align-items-center justify-content-center">
                                                <i class="material-icons text-info me-1">email</i>
                                                <span>{{ $u->email }}</span>
                                            </a>
                                        </span>
                                        </td>

                                        <td class="align-middle text-center">
                                        <span class="text-secondary text-s font-weight-bold">

                                            {{-- @endcan --}}

                                            @can('admin-edit')
                                                <a rel="tooltip" class="btn btn-success btn-link" href="{{ route('edit-admin', $u->id) }}" data-original-title="" title="">
                                                <i class="material-icons">edit</i>
                                                <div class="ripple-container"></div>
                                            </a>
                                            @endcan

                                            @can('admin-delete')
                                                <button type="button" class="btn btn-danger btn-link" data-original-title="" title="" onclick="confirmDelete({{ $u->id }})">
                                                <i class="material-icons">close</i>
                                                <div class="ripple-container"></div>
                                            </button>
                                            @endcan
                                        </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6">
                                        {{ $admins->links() }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
