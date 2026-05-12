<div>
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
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="overlay">
                <div class="loader"></div>
            </div>
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    Create role
                    <span class="float-right mt-2">
                        <a class="btn bg-gradient-primary" href="{{ route('roles') }}">Roles</a>
                    </span>
                </div>
                <div class="card-body role-body">
                    <div class="row">


                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Role Name</label>
                            <input wire:model="name" type="text" class="form-control border border-2 p-2" id="name" placeholder="Enter customer name">
                            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Search Role</label>
                            <input wire:model.live="searchTerm" type="text" class="form-control border border-2 p-2" placeholder="Search roles" aria-label="Search roles">

                            @error('searchTerm') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>


                    <div class="card">
                        <div class="card-header d-flex align-items-center gap-4">
                            <h3><b>Permissions</b></h3>
                            <div class="form-check">
                                <input wire:click="selectAllPermissions" class="form-check-input" type="checkbox" wire:model="selectAll" value="" id="selectall">
                                <label class="form-check-label" for="selectall">
                                    Select All
                                </label>
                            </div>
                        </div>
                        <div class="px-4">
                            @foreach($filteredPermissions as $permissionkey => $value)
                                <div class="table-responsive">
                                <table class="table table-striped border-2 border-dark w-100">
                                    <thead>
                                    <tr>
                                        <th>
                                            <span style="font-size: 22px;">{{$permissionkey}}</span>
                                            <!-- Select All for each group -->
                                            <div class="form-check d-inline-block ms-2">
                                                <input wire:click="selectGroupPermissions('{{$permissionkey}}')"
                                                       class="form-check-input"
                                                       type="checkbox"
                                                       wire:model="groupSelectAll.{{$permissionkey}}"
                                                       id="groupSelectAll_{{$permissionkey}}">
                                                <label class="form-check-label" for="groupSelectAll_{{$permissionkey}}">
                                                    Select All
                                                </label>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="col">Permission</th>
                                        <th scope="col">Detail</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($value as $key => $v)
                                        <tr>
                                            <td class="ps-4 w-50">
                                                <div class="form-check">
                                                    <input wire:model="selectedPermission.{{ $v['id'] }}"
                                                           class="form-check-input"
                                                           type="checkbox"
                                                           value=""
                                                           id="{{ $v['id'] }}">
                                                    <label class="form-check-label role-check-label" for="selectedPermission.{{ $v['name'] }}">
                                                        {{$v['name']}}
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="ps-4 w-50" wire:ignore>
                                                <label class="form-check-label role-check-label" for="selectedPermission.{{ $v['name'] }}">
                                                    {{$v['permissionSubName']}}
                                                </label>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @error('role.selectedPermission') <div class="text-danger">{{ $message }}</div> @enderror
                    <div class="d-flex justify-content-end">
                        <button type="submit" wire:click="store" wire:loading.attr="disabled" wire:target="store" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ 'Save' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
