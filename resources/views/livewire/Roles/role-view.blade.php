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
                        <h6 class="text-white mx-3"><strong> Add, Edit, Delete Roles</strong> </h6>
                    </div>
                </div>
                <div class=" me-3 my-3 text-end">
                    <a class="btn bg-gradient-dark mb-0" href="{{ route('add-role') }}"><i
                            class="material-icons text-sm">add</i>&nbsp;&nbsp;Add New
                        Role</a>
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
                                        Name
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($role as $key => $r)
                                    <tr>
                                        <td class="text-center align-middle">
                                            <p class="text-s font-weight-bold mb-0">{{ $key+1 }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-s font-weight-bold mb-0">{{ $r->name }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            @if($r->id != 1)
                                                @can('role-edit')
                                                    <a rel="tooltip" class="btn btn-success btn-link"
                                                        href="{{ route('edit-role', $r->id) }}" data-original-title=""
                                                        title="">
                                                        <i class="material-icons">edit</i>
                                                        <div class="ripple-container"></div>
                                                    </a>
                                                @endcan
                                                @if($r->id != 2)
                                                    @can('role-delete')
                                                        <button type="button" class="btn btn-danger btn-link"
                                                            data-original-title="" title=""
                                                            onclick="confirmDelete({{ $r->id }})">
                                                            <i class="material-icons">close</i>
                                                            <div class="ripple-container"></div>
                                                        </button>
                                                    @endcan
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6">
                                        {{ $role->links() }}
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
