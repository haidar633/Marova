@php
    $userPreferences = auth()->user()->preferences()->first();
    $activeColor = $userPreferences ? $userPreferences->sidebar_color : 'primary';
    $isDarkMode = $userPreferences && $userPreferences->dark_mode ? true : false;
    $textColor = $isDarkMode ? '#ffffff' : '#000000';
    $bgColor = $isDarkMode ? '#343a40' : '#ffffff';
@endphp


<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 {{ $isDarkMode ? '#344767 ' : 'bg-white' }}"
    id="sidenav-main">

    <div
        class="sidenav-header sticky-top py-2 d-flex align-items-center justify-content-center {{ $isDarkMode ? 'bg-dark' : 'bg-white' }}">
        <i class="fas fa-times p-3 cursor-pointer position-absolute d-none d-xl-none"
           aria-hidden="true" id="iconSidenav" style="color: #1e293f; top: 0; right: 0;"></i>

        <a class="navbar-brand text-center d-flex align-items-center justify-content-center gap-2"
           @can('dashboard-view') href="{{ route('dashboard') }}" @endcan
           style="color: {{ $textColor }}; padding-top: 0; padding-bottom: 0; text-decoration: none; font-family: 'Brush Script MT', cursive; font-size: 3rem;">
            <span style="font-size: 1.9rem;">🍑</span>
            <span style="color: #e91e63; font-weight: bold;">
        Marova
    </span>
            <span style="font-size: 1.9rem;">💋</span>
        </a>
    </div>

    <hr class="{{ $isDarkMode ? 'border-light' : '' }} horizontal mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto h-auto overflow-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">


            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-8"
                    style="color: {{ $textColor }};">Activity & Behavior</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'dashboard' ? 'active bg-gradient-' . $activeColor . ' text-white' : '' }}"
                   href="{{ route('dashboard') }}"
                   style="color: {{ Route::currentRouteName() == 'dashboard' ? '#ffffff' : $textColor }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-lg fa-tachometer-alt ps-2 pe-2 text-center" style="color: inherit;"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ in_array(Route::currentRouteName(), ['attempts', 'add-attempt', 'edit-attempt', 'view-attempt']) ? 'active bg-gradient-' . $activeColor . ' text-white' : '' }}"
                   href="{{ route('attempts') }}"
                   style="color: {{ Route::currentRouteName() == 'attempts' ? '#ffffff' : $textColor }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-lg fa-history ps-2 pe-2 text-center" style="color: inherit;"></i>
                    </div>
                    <span class="nav-link-text ms-1">Attempts</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'spin-wheel' ? 'active bg-gradient-' . $activeColor . ' text-white' : '' }}"
                   href="{{ route('spin-wheel') }}"
                   style="color: {{ Route::currentRouteName() == 'spin-wheel' ? '#ffffff' : $textColor }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-lg fa-compact-disc ps-2 pe-2 text-center" style="color: inherit;"></i>
                    </div>
                    <span class="nav-link-text ms-1">Spin the Wheel</span>
                </a>
            </li>


            {{--                <li class="nav-item">--}}
            {{--                    <a class="nav-link {{ Route::currentRouteName() == 'statistics' ? 'active bg-gradient-' . $activeColor . ' text-white' : '' }}"--}}
            {{--                       href="{{ route('statistics') }}"--}}
            {{--                       style="color: {{ Route::currentRouteName() == 'statistics' ? '#ffffff' : $textColor }};">--}}
            {{--                        <div class="text-center me-2 d-flex align-items-center justify-content-center">--}}
            {{--                            <i class="fas fa-lg fa-chart-bar ps-2 pe-2 text-center" style="color: inherit;"></i>--}}
            {{--                        </div>--}}
            {{--                        <span class="nav-link-text ms-1">Statistics & Analytics</span>--}}
            {{--                    </a>--}}
            {{--                </li>--}}

            {{--                <li class="nav-item">--}}
            {{--                    <a class="nav-link {{ Route::currentRouteName() == 'behavioral-insights' ? 'active bg-gradient-' . $activeColor . ' text-white' : '' }}"--}}
            {{--                       href="{{ route('behavioral-insights') }}"--}}
            {{--                       style="color: {{ Route::currentRouteName() == 'behavioral-insights' ? '#ffffff' : $textColor }};">--}}
            {{--                        <div class="text-center me-2 d-flex align-items-center justify-content-center">--}}
            {{--                            <i class="fas fa-lg fa-brain ps-2 pe-2 text-center" style="color: inherit;"></i>--}}
            {{--                        </div>--}}
            {{--                        <span class="nav-link-text ms-1">Behavioral Insights</span>--}}
            {{--                    </a>--}}
            {{--                </li>--}}
            {{--             good icons::   fa-user-friends     fa-notes-medical    fa-list-alt           --}}
            <li class="nav-item">
                <a class="nav-link {{ in_array(Route::currentRouteName(), ['positions', 'add-position', 'edit-position','favorite-positions']) ? 'active bg-gradient-' . $activeColor . ' text-white' : '' }}"
                   href="{{ route('positions') }}"
                   style="color: {{ in_array(Route::currentRouteName(), ['positions', 'add-position', 'edit-position','favorite-positions']) ? '#ffffff' : $textColor }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-lg fa-heartbeat ps-2 pe-2 text-center" style="color: inherit;"></i>
                    </div>
                    <span class="nav-link-text ms-1">Intimacy</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'sex-tracking' ? 'active bg-gradient-' . $activeColor . ' text-white' : '' }}"
                   href="{{ route('sex-tracking') }}"
                   style="color: {{ Route::currentRouteName() == 'sex-tracking' ? '#ffffff' : $textColor }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-lg fa-heartbeat ps-2 pe-2 text-center" style="color: inherit;"></i>
                    </div>
                    <span class="nav-link-text ms-1">Sex Tracking</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ in_array(Route::currentRouteName(), ['partners', 'add-partner', 'edit-partner']) ? 'active bg-gradient-' . $activeColor . ' text-white' : '' }}"
                   href="{{ route('partners') }}"
                   style="color: {{ in_array(Route::currentRouteName(), ['partners', 'add-partner', 'edit-partner']) ? '#ffffff' : $textColor }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-lg fa-user-friends ps-2 pe-2 text-center" style="color: inherit;"></i>
                    </div>
                    <span class="nav-link-text ms-1">Partners</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'masturbation-tracker' ? 'active bg-gradient-' . $activeColor . ' text-white' : '' }}"
                   href="{{ route('masturbation-tracker') }}"
                   style="color: {{ Route::currentRouteName() == 'masturbation-tracker' ? '#ffffff' : $textColor }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-lg fa-user-secret ps-2 pe-2 text-center" style="color: inherit;"></i>
                    </div>
                    <span class="nav-link-text ms-1">Masturbation Tracker</span>
                </a>
            </li>

            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-8"
                    style="color: {{ $textColor }};">Emotional & Communication</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ in_array(Route::currentRouteName(), ['mood-journals', 'add-mood-journal', 'edit-mood-journal', 'show-mood-journal']) ? 'active bg-gradient-' . $activeColor . ' text-white' : '' }}"
                   href="{{ route('mood-journals') }}"
                   style="color: {{ in_array(Route::currentRouteName(), ['mood-journals', 'add-mood-journal', 'edit-mood-journal', 'show-mood-journal']) ? '#ffffff' : $textColor }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-lg fa-book ps-2 pe-2 text-center" style="color: inherit;"></i>
                    </div>
                    <span class="nav-link-text ms-1">Mood Journal</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ in_array(Route::currentRouteName(), ['vibe-checks', 'add-vibe-check', 'edit-vibe-check']) ? 'active bg-gradient-' . $activeColor . ' text-white' : '' }}"
                   href="{{ route('vibe-checks') }}"
                   style="color: {{ in_array(Route::currentRouteName(), ['vibe-checks', 'add-vibe-check', 'edit-vibe-check']) ? '#ffffff' : $textColor }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-lg fa-heartbeat ps-2 pe-2 text-center" style="color: inherit;"></i>
                    </div>
                    <span class="nav-link-text ms-1">Vibe Check</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ in_array(Route::currentRouteName(), ['talk-trackers', 'add-talk-tracker', 'edit-talk-tracker', 'show-talk-tracker']) ? 'active bg-gradient-' . $activeColor . ' text-white' : '' }}"
                   href="{{ route('talk-trackers') }}"
                   style="color: {{ in_array(Route::currentRouteName(), ['talk-trackers', 'add-talk-tracker', 'edit-talk-tracker', 'show-talk-tracker']) ? '#ffffff' : $textColor }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-lg fa-comments ps-2 pe-2 text-center" style="color: inherit;"></i>
                    </div>
                    <span class="nav-link-text ms-1">Talk Tracker</span>
                </a>
            </li>

            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-8"
                    style="color: {{ $textColor }};">Content Organization</h6>
            </li>
            <li class="nav-item">

                <a class="nav-link {{ in_array(Route::currentRouteName(), ['favorite-hubs', 'add-favorite-hub']) ? 'active bg-gradient-' . $activeColor . ' text-white' : '' }}"
                   href="{{ route('favorite-hubs') }}"
                   style="color: {{ in_array(Route::currentRouteName(), ['favorite-hubs', 'add-favorite-hub']) ? '#ffffff' : $textColor }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-lg fa-heart ps-2 pe-2 text-center" style="color: inherit;"></i>
                    </div>
                    <span class="nav-link-text ms-1">Favorites Hub</span>
                </a>
            </li>


            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-8"
                    style="color: {{ $textColor }};">Reports</h6>
            </li>


            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'activity-tracker' ? 'active bg-gradient-' . $activeColor . ' text-white' : '' }}"
                   href="{{ route('activity-tracker') }}"
                   style="color: {{ Route::currentRouteName() == 'activity-tracker' ? '#ffffff' : $textColor }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-lg fa-running ps-2 pe-2 text-center" style="color: inherit;"></i>
                    </div>
                    <span class="nav-link-text ms-1">Activity Tracker</span>
                </a>
            </li>


            <li class="nav-item d-block d-md-none">
                <a class="nav-link"
                   onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                   style="color: {{ $textColor }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i style="font-size: 1rem;color: {{ $textColor }};"
                           class="fas fa-lg fa-lg fa-sign-out-alt ps-2 pe-2 text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">Sign Out</span>
                </a>
                <form id="logout-form-mobile" method="POST" action="{{ route('logout') }}" style="display: none;">
                    @csrf
                </form>
            </li>


        </ul>
    </div>
</aside>
@push('js')

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('refresh-page', () => {
                // Small delay to ensure preferences are saved
                setTimeout(() => {
                    window.location.reload();
                }, 0);
            });
        });
    </script>
@endpush
