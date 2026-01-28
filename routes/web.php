<?php

use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\TranslationsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Client\ActivityController;
use App\Http\Controllers\Client\DealController;
use App\Http\Controllers\Client\GoalController;
use App\Http\Controllers\Client\MeetingController;
use App\Http\Controllers\Client\MemberController;
use App\Http\Controllers\Client\OneToOneController;
//use App\Http\Controllers\Client\TaskController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\TaskController;
use App\Http\Controllers\Client\TeamController;
use App\Http\Controllers\Client\TodoController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\HubspotController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Client\PipelineController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/home');
});

// Health check endpoint (public, for monitoring)
Route::get('/health', [\App\Http\Controllers\HealthController::class, 'check'])->name('health');

// Public Documentation Routes (no authentication required)
Route::get('/docs/hubspot-setup-guide', [\App\Http\Controllers\DocumentationController::class, 'hubspotSetupGuide'])->name('docs.hubspot-setup-guide');
Route::get('/docs/shared-data', [\App\Http\Controllers\DocumentationController::class, 'sharedData'])->name('docs.shared-data');
Route::get('/docs/scope-justification', [\App\Http\Controllers\DocumentationController::class, 'scopeJustification'])->name('docs.scope-justification');
Route::get('/terms-of-service', [\App\Http\Controllers\DocumentationController::class, 'termsOfService'])->name('docs.terms-of-service');
Route::get('/privacy-policy', [\App\Http\Controllers\DocumentationController::class, 'privacyPolicy'])->name('docs.privacy-policy');
Route::get('/security-policy', [\App\Http\Controllers\DocumentationController::class, 'securityPolicy'])->name('docs.security-policy');

// Google URL
Route::prefix('google')->name('google.')->group( function(){
    Route::get('login', [GoogleController::class, 'googleRedirect'])->name('login');
    Route::get('callback', [GoogleController::class, 'googleCallback'])->name('callback');
});

// Hubspot URL
Route::prefix('hubspot')->name('hubspot.')->group( function(){
    Route::get('install', [HubspotController::class, 'install'])->name('install'); // Public install page for marketplace
    Route::get('login', [HubspotController::class, 'hubspotRedirect'])->name('login');
    Route::get('callback', [HubspotController::class, 'hubspotCallback'])->name('callback');
});

Route::group(['middleware'=> ['auth', 'impersonate']], function() {
    Route::get( 'home', function () {
        if( Auth::user()->isAdmin() ) return view('admin.dashboard');//return redirect('/admin/user');
        else return redirect('/client/home');
    })->name('home');

    Route::get('/impersonate/stop', [ UsersController::class, 'stopImpersonate' ] )->name('impersonate.stop');

    Route::get('notification', [NotificationsController::class, 'index'])-> name( 'notification.index' );
    Route::get('notification/{notification}', [NotificationsController::class, 'show'])-> name( 'notification.show' );
    Route::get('notification/{notification}/details', [NotificationsController::class, 'details'])-> name( 'notification.details' );
    Route::get('user/notifications', [NotificationsController::class, 'notifications'])-> name( 'user.notifications' );
    Route::get('user/{user}', [UsersController::class, 'show'])->name('user.show');

    Route::resource( 'organization', OrganizationController::class )->only(['edit', 'update']);

    Route::group(['middleware' => 'role:admin', 'prefix' => 'admin', 'as'=> 'admin.'], function() {
        Route::get('/user/{id}/impersonate', [ UsersController::class, 'impersonate' ] );

        Route::post( 'user/datatable', [ UsersController::class, 'datatable' ] )->name( 'user.datatable' );
        Route::get('user/notify/{user}/create', [NotificationsController::class, 'create'])-> name( 'user.notify.create' );
        Route::post( 'user/notify/{user}', [NotificationsController::class, 'send'] )-> name( 'user.notify' );
        Route::resource( 'user', UsersController::class )->except(['show']);

        Route::post('translation/datatable', [TranslationsController::class, 'datatable'])-> name( 'translation.datatable' );
        Route::resource( 'translation', TranslationsController::class );

        Route::post('organization/datatable', [OrganizationController::class, 'datatable'])-> name( 'organization.datatable' );
        Route::resource( 'organization', OrganizationController::class )->only(['index', 'store', 'destroy']);
    });

    Route::group(['middleware' => ['role:user', 'role:owner' ], 'prefix' => 'client', 'as'=> 'client.'], function() {
        Route::get( 'home', [\App\Http\Controllers\Client\DashboardController::class, 'index'])->name('home');
        //Route::resource( 'user', \App\Http\Controllers\User\UsersController::class )->only(['show', 'update', 'destroy']);
        Route::get( 'pipeline/sync', [ PipelineController::class, 'sync_hubspot_pipelines' ] )->middleware('throttle:hubspot-sync')->name( 'pipeline.sync' );
        Route::post( 'pipeline/datatable', [ PipelineController::class, 'datatable' ] )->name( 'pipeline.datatable' );
        Route::resource( 'pipeline', PipelineController::class )->only(['index', 'update']);

        Route::get( 'member/sync', [ MemberController::class, 'sync_hubspot_owners' ] )->middleware('throttle:hubspot-sync')->name( 'member.sync' );
        Route::post( 'member/datatable', [ MemberController::class, 'datatable' ] )->name( 'member.datatable' );
        Route::post( 'member/get', [ MemberController::class, 'get_members' ] )->name( 'member.get' );
        Route::resource( 'member', MemberController::class )->only(['index', 'update']);

        Route::get( 'deal/sync', [ DealController::class, 'sync_hubspot_deals' ] )->middleware('throttle:hubspot-sync')->name( 'deal.sync' );
        Route::get( 'deal/{deal}/stages', [ DealController::class, 'stages' ] )->name( 'deal.stages' );
        Route::post( 'deal/datatable', [ DealController::class, 'datatable' ] )->name( 'deal.datatable' );
        //Route::get('deal/{deal?}/task/create', [TaskController::class, 'create'])->name('deal.task.create');
        //Route::post('deal/{deal?}/task', [TaskController::class, 'store'])->name('deal.task.store');
        Route::post( 'deal/{deal}/activity/datatable', [ ActivityController::class, 'datatable' ] )->name( 'deal.activity.datatable' );
        Route::resource( 'deal', DealController::class )->only(['index', 'update', 'edit']);

        Route::get('hubspot/sync', [HubspotController::class, 'sync_all'])->middleware('throttle:hubspot-sync')->name('hubspot.sync');

        Route::get('team/manage', [TeamController::class, 'manage_teams'])->name('team.manage');
        Route::get('team/get', [TeamController::class, 'get_teams'])->name('team.get');
        Route::get('team/create', [TeamController::class, 'create'])->name('team.create');
        Route::post('team', [TeamController::class, 'store'])->name('team.store');
        Route::get('team/{team}/edit', [TeamController::class, 'edit'])->name('team.edit');
        Route::put('team/{team}', [TeamController::class, 'update'])->name('team.update');
        Route::get('team/{team}/delete', [TeamController::class, 'delete'])->name('team.delete');
        Route::delete('team/{team}', [TeamController::class, 'destroy'])->name('team.destroy');
        Route::put('team/{team}/members', [TeamController::class, 'add_members'])->name('team.members.add');
        //Route::get('team/{team}/goals', [TeamController::class, 'goals'])->name('team.goals');
        Route::delete('team/{team}/members', [TeamController::class, 'delete_member'])->name('team.members.delete');

        Route::get('goal/manage/team/{team}/member/{member}', [GoalController::class, 'manage_goals'])->name('goal.manage');
        Route::resource( 'goal', GoalController::class )->only(['update', 'destroy']);

        Route::post('1-1/team/{team}/members/datatable', [OneToOneController::class, 'teamMembersDatatable'])->name('1-1.team.members.datatable');
        Route::resource('1-1', OneToOneController::class)->only(['index']);

        // Google Calendar 1-on-1 meetings API endpoint
        Route::get('one-on-ones', [\App\Http\Controllers\Client\OneOnOneMeetingController::class, 'index'])->name('one-on-ones.index');

        // AI Deal Briefing endpoint
        Route::get('deal/{deal}/briefing', [\App\Http\Controllers\Client\DealBriefingController::class, 'generate'])->middleware('throttle:ai-briefing')->name('deal.briefing');

        Route::post('meeting/{meeting}/todos/datatable', [TodoController::class, 'todosDatatable'])->name('meeting.todos.datatable');
        Route::post('meeting/{meeting}/todos', [TodoController::class, 'store'])->name('meeting.todos.store');
        Route::put('meeting/{meeting}/todos/{todo}', [TodoController::class, 'update'])->name('meeting.todos.update');
        Route::delete('meeting/{meeting}/todos/{todo}', [TodoController::class, 'destroy'])->name('meeting.todos.destroy');
        Route::get('meeting/{meeting}/schedule', [MeetingController::class, 'schedule'])->name('meeting.schedule');
        Route::get('meeting/{meeting}/schedule/edit', [MeetingController::class, 'schedule_edit'])->name('meeting.schedule.edit');
        Route::post('meeting/{meeting}/schedule', [MeetingController::class, 'schedule_store'])->name('meeting.schedule.store');
        Route::post('meeting/datatable', [MeetingController::class, 'meetingsDatatable'])->name('meeting.datatable');
        Route::resource('meeting', MeetingController::class)->only(['index','store','edit','update']);

        //Route::get( 'task/sync', [ TaskController::class, 'sync_hubspot_tasks' ] )->name( 'task.sync' );
        //Route::post( 'task/datatable', [ TaskController::class, 'datatable' ] )->name( 'task.datatable' );
        //Route::get( 'task/create', [ TaskController::class, 'create' ] )->name( 'task.create' );
        //Route::post('task/{deal?}', [TaskController::class, 'store'])->name('task.store');
        Route::resource( 'task', TaskController::class )->except(['index', 'show']);

        Route::get('profile/{profile}', [ProfileController::class, 'show'])->name('profile.show');
    });

});
