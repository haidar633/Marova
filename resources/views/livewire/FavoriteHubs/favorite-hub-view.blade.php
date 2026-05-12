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
                        <h6 class="text-white mx-3"><strong> Add, Edit, Delete Favorite Hubs</strong> </h6>
                    </div>
                </div>
                <div class=" me-3 my-3 text-end">
                    <a class="btn bg-gradient-dark mb-0" href="{{ route('add-favorite-hub') }}"><i
                            class="material-icons text-sm">add</i>&nbsp;&nbsp;Add New Favorite Hub</a>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        ID
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Link
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Description
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Actions
                                    </th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($favoriteHubs as $key => $u)
                                <tr>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-s font-weight-bold">
                                                {{ $key+1 }}
                                            </span>

                                        </td>

                                    <td class="align-middle text-center">
                                        <a href="{{ $u->link }}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="material-icons text-sm">link</i> Visit
                                        </a>
                                    </td>


                                    <td class="align-middle text-center">
                                            <span class="text-secondary text-s font-weight-bold">
                                                {{ $u->description }}</span>

                                        </td>

                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-s font-weight-bold">
{{--                                            @can('expense-edit')--}}
{{--                                                <a rel="tooltip" class="btn btn-success btn-link"--}}
{{--                                                    href="{{ route('edit-expense', $u->id) }}" data-original-title=""--}}
{{--                                                    title="">--}}
{{--                                                    <i class="material-icons">edit</i>--}}
{{--                                                    <div class="ripple-container"></div>--}}
{{--                                                </a>--}}
{{--                                            @endcan--}}
                                            @can('favorate-hub-delete')
                                                <button type="button" class="btn btn-danger btn-link"
                                                    data-original-title="" title=""
                                                    onclick="confirmDelete({{ $u->id }})">
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
                                <td colspan="6" class="text-center">
                                    {{ $favoriteHubs->links() }}
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
