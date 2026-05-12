<?php

use App\Http\Livewire\ActivityTracker;
use App\Http\Livewire\Admins\AdminAdd;
use App\Http\Livewire\Admins\AdminEdit;
use App\Http\Livewire\Admins\AdminView;
use App\Http\Livewire\AppointmentsView;
use App\Http\Livewire\Attempts\AttemptAdd;
use App\Http\Livewire\Attempts\AttemptEdit;
use App\Http\Livewire\Attempts\AttemptView;
use App\Http\Livewire\Attempts\AttemptShow;
use App\Http\Livewire\Auth\ForgotPassword;
use App\Http\Livewire\Auth\Login;
use App\Http\Livewire\Auth\Register;
use App\Http\Livewire\Auth\ResetPassword;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\FavoriteHubs\FavoriteHubAdd;
use App\Http\Livewire\FavoriteHubs\FavoriteHubView;
use App\Http\Livewire\FavPositions\FavPositionView;
use App\Http\Livewire\MasturbationTracker;
use App\Http\Livewire\Patients\PatientAdd;
use App\Http\Livewire\Patients\PatientEdit;
use App\Http\Livewire\Patients\PatientView;
use App\Http\Livewire\Partners\PartnerAdd;
use App\Http\Livewire\Partners\PartnerEdit;
use App\Http\Livewire\Partners\PartnerView;
use App\Http\Livewire\Positions\PositionAdd;
use App\Http\Livewire\Positions\PositionEdit;
use App\Http\Livewire\Positions\PositionView;
use App\Http\Livewire\Reports\ReportView;
use App\Http\Livewire\Roles\RoleAdd;
use App\Http\Livewire\Roles\RoleEdit;
use App\Http\Livewire\Roles\RoleView;
use App\Http\Livewire\SexTracking;
use App\Http\Livewire\SpinWheel;
use App\Http\Livewire\Staffs\StaffAdd;
use App\Http\Livewire\Staffs\StaffEdit;
use App\Http\Livewire\Staffs\StaffView;
use App\Http\Livewire\UserManagement\UserProfile;
use App\Http\Livewire\MoodJournals\MoodJournalView;
use App\Http\Livewire\MoodJournals\MoodJournalAdd;
use App\Http\Livewire\MoodJournals\MoodJournalEdit;
use App\Http\Livewire\MoodJournals\MoodJournalShow;
use App\Http\Livewire\VibeChecks\VibeCheckView;
use App\Http\Livewire\VibeChecks\VibeCheckAdd;
use App\Http\Livewire\VibeChecks\VibeCheckEdit;
use App\Http\Livewire\TalkTrackers\TalkTrackerView;
use App\Http\Livewire\TalkTrackers\TalkTrackerAdd;
use App\Http\Livewire\TalkTrackers\TalkTrackerEdit;
use App\Http\Livewire\TalkTrackers\TalkTrackerShow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('forgot-password', ForgotPassword::class)->middleware('guest')->name('password.forgot');
Route::get('reset-password/{id}', ResetPassword::class)->middleware('signed')->name('reset-password');
Route::get('sign-up', Register::class)->middleware('guest')->name('register');
Route::get('sign-in', Login::class)->middleware('guest')->name('login');
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/sign-in');
})->name('logout');



/** Administration **/
Route::group(['prefix' => 'roles', 'middleware' => 'auth'], function () {
    Route::get('/', RoleView::class)->name('roles');
    Route::get('/add', RoleAdd::class)->name('add-role');
    Route::get('/edit/{id}', RoleEdit::class)->name('edit-role');
});

Route::group(['prefix' => 'staffs', 'middleware' => 'auth'], function () {
    Route::get('/', StaffView::class)->name('staffs');
    Route::get('/add', StaffAdd::class)->name('add-staff');
    Route::get('/edit/{id}', StaffEdit::class)->name('edit-staff');
    Route::get('/edit/{id}', StaffEdit::class)->middleware('block.id.1')->name('edit-staff');
});

Route::group(['prefix' => 'admins', 'middleware' => 'auth'], function () {
    Route::get('/', AdminView::class)->name('admins');
    Route::get('/add', AdminAdd::class)->name('add-admin');
    Route::get('/edit/{id}', AdminEdit::class)->name('edit-admin');
    Route::get('/edit/{id}', AdminEdit::class)->middleware('block.id.1')->name('edit-admin');

});


Route::group(['prefix' => 'reports', 'middleware' => 'auth'], function () {
    Route::get('/', ReportView::class)->name('reports');
});


Route::get('user-profile', UserProfile::class)->middleware('auth')->name('user-profile');





Route::get('/activity-tracker', ActivityTracker::class)->middleware('auth')->name('activity-tracker');







Route::get('/dashboard', Dashboard::class)->middleware('auth')->name('dashboard');


Route::group(['prefix' => 'attempts', 'middleware' => 'auth'], function () {
    Route::get('/', AttemptView::class)->name('attempts');
    Route::get('/view/{id}', AttemptShow::class)->name('view-attempt');
    Route::get('/add', AttemptAdd::class)->name('add-attempt');
    Route::get('/edit/{id}', AttemptEdit::class)->name('edit-attempt');
});

//Route::group(['prefix' => 'statistics', 'middleware' => 'auth'], function () {
//    Route::get('/', PatientView::class)->name('statistics');
//    Route::get('/add', PatientView::class)->name('add-statistics');
//    Route::get('/edit/{id}', PatientView::class)->name('edit-statistics');
//});

//Route::group(['prefix' => 'behavioral-insights', 'middleware' => 'auth'], function () {
//    Route::get('/', PatientView::class)->name('behavioral-insights');
//    Route::get('/add', PatientView::class)->name('add-behavioral-insights');
//    Route::get('/edit/{id}', PatientView::class)->name('edit-behavioral-insights');
//});

Route::group(['prefix' => 'intimacy', 'middleware' => 'auth'], function () {
    Route::get('/', PositionView::class)->name('positions');
    Route::get('/add', PositionAdd::class)->name('add-position');
    Route::get('/edit/{id}', PositionEdit::class)->name('edit-position');
    Route::get('/favorites', FavPositionView::class)->name('favorite-positions');
});

Route::group(['prefix' => 'partners', 'middleware' => 'auth'], function () {
    Route::get('/', PartnerView::class)->name('partners');
    Route::get('/add', PartnerAdd::class)->name('add-partner');
    Route::get('/edit/{id}', PartnerEdit::class)->name('edit-partner');
});



Route::group(['prefix' => 'favorite-hubs', 'middleware' => 'auth'], function () {
    Route::get('/', FavoriteHubView::class)->name('favorite-hubs');
    Route::get('/add', FavoriteHubAdd::class)->name('add-favorite-hub');
//    Route::get('/edit/{id}', FavoriteHubEdit::class)->name('edit-favorites-hub');
});



//Route::group(['prefix' => 'tags-categories', 'middleware' => 'auth'], function () {
//    Route::get('/', PatientView::class)->name('tags-categories');
//    Route::get('/add', PatientView::class)->name('add-tags-categories');
//    Route::get('/edit/{id}', PatientView::class)->name('edit-tags-categories');
//});

Route::get('/spinWheel', SpinWheel::class)->name('spin-wheel');

Route::get('/sexTracking', SexTracking::class)->name('sex-tracking');
Route::get('/masturbate', MasturbationTracker::class)->name('masturbation-tracker');

// Mood Journal routes
Route::group(['prefix' => 'mood-journals', 'middleware' => 'auth'], function () {
    Route::get('/', MoodJournalView::class)->name('mood-journals');
    Route::get('/add', MoodJournalAdd::class)->name('add-mood-journal');
    Route::get('/edit/{id}', MoodJournalEdit::class)->name('edit-mood-journal');
    Route::get('/show/{id}', MoodJournalShow::class)->name('show-mood-journal');
});

// Vibe Check routes
Route::group(['prefix' => 'vibe-checks', 'middleware' => 'auth'], function () {
    Route::get('/', VibeCheckView::class)->name('vibe-checks');
    Route::get('/add', VibeCheckAdd::class)->name('add-vibe-check');
    Route::get('/edit/{id}', VibeCheckEdit::class)->name('edit-vibe-check');
});

// Talk Tracker routes
Route::group(['prefix' => 'talk-trackers', 'middleware' => 'auth'], function () {
    Route::get('/', TalkTrackerView::class)->name('talk-trackers');
    Route::get('/add', TalkTrackerAdd::class)->name('add-talk-tracker');
    Route::get('/edit/{id}', TalkTrackerEdit::class)->name('edit-talk-tracker');
    Route::get('/show/{id}', TalkTrackerShow::class)->name('show-talk-tracker');
});
