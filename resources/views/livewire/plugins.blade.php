<div class="fixed-plugin">
    @if(auth()->check())
{{--    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">--}}
{{--        <i class="material-icons py-2">settings</i>--}}
{{--    </a>--}}
    @endif
    <div class="card shadow-lg">
        <div class="card-header pb-0 pt-3">
            <div class="float-start">
                <h5 class="mt-3 mb-0">UI Configurator</h5>
                <p>See our dashboard options.</p>
            </div>
            <div class="float-end mt-4">
                <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
                    <i class="material-icons">clear</i>
                </button>
            </div>
        </div>
        <hr class="horizontal dark my-1">
        <div class="card-body pt-sm-3 pt-0">
            <!-- Sidebar Colors -->
            <div>
                <h6 class="mb-0">Sidebar Colors</h6>
            </div>
            <div class="badge-colors my-2 text-start">
                @foreach(['primary', 'dark', 'info', 'success', 'warning', 'danger'] as $color)
                    <span
                        wire:click="$set('sidebarColor', '{{ $color }}')"
                        class="badge filter bg-gradient-{{ $color }} {{ $sidebarColor === $color ? 'active' : '' }}"
                        data-color="{{ $color }}">
                    </span>
                @endforeach
            </div>

            <hr class="horizontal dark my-3">
{{--            <div class="mt-2 d-flex">--}}
{{--                <h6 class="mb-0">Light / Dark</h6>--}}
{{--                <div class="form-check form-switch ps-0 ms-auto my-auto">--}}
{{--                    <input--}}
{{--                        class="form-check-input mt-1 ms-auto"--}}
{{--                        type="checkbox"--}}
{{--                        wire:model.live="darkMode"--}}
{{--                        id="dark-version">--}}

{{--                </div>--}}
{{--            </div>--}}
        </div>
    </div>
</div>
