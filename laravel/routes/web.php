<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\admin\DBEditorController;
use App\Http\Controllers\MedhubController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\ScheduleDataController;

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

Route::prefix('/')->group(function () {
    Route::get('', function () {
        return redirect('/resident/schedule/secondday');
    });
    Route::get('about', [PagesController::class, 'getAbout']);
    Route::get('contact', [PagesController::class, 'getContact']);
    Route::post('contact', [PagesController::class, 'postContact']);
    Route::get('test', [PagesController::class, 'test']);
    Route::get('acknowledgements', [PagesController::class, 'getAcknowledgements']);
});

// resident pages
Route::group(['prefix' => 'resident', 'middleware' => 'resident'], function () {
    Route::get('/', [ResidentController::class, 'getIndex']);
    Route::get('home', [PagesController::class, 'getIndex']);
    Route::get('about', [PagesController::class, 'getAbout']);
    Route::get('contact', [PagesController::class, 'getContact']);
    Route::post('contact', [PagesController::class, 'postContact']);
    Route::get('acknowledgements', [PagesController::class, 'getAcknowledgements']);
    Route::get('schedule', [ResidentController::class, 'getSchedule']);
    Route::get('schedule/{day}', [ScheduleDataController::class, 'getDay']);
    Route::get('schedule/{day}/filter/{room}/{leadSurgeon}/{rotation}/{starttime_endtime}', [
        ScheduleDataController::class,
        'getDay',
    ]);
    Route::post('schedule/confirm', [ScheduleDataController::class, 'getChoice']);
    Route::get('schedule/milestones/{id}', [ScheduleDataController::class, 'selectMilestones']);
    Route::get('schedule/milestonesEdit/{id}', [ScheduleDataController::class, 'updateMilestones']);
    Route::get('schedule/secondday/preferences/clear/{date}', [ScheduleDataController::class, 'clearOption']);
    Route::get('schedule/thirdday/preferences/clear/{date}', [ScheduleDataController::class, 'clearOption']);
    Route::post('schedule/submit', [ScheduleDataController::class, 'postSubmit']);
    Route::get('instructions', [ResidentController::class, 'getInstructions']);
    Route::get('postmessage', [ResidentController::class, 'getMessages']);
    Route::post('announcement', [PagesController::class, 'postAnnouncement']);
    Route::post('deleteannouncement', [PagesController::class, 'deleteAnnouncement']);
});

// admin pages
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::get('/', [AdminController::class, 'getIndex']);
    Route::get('home', [PagesController::class, 'getIndex']);
    Route::get('about', [PagesController::class, 'getAbout']);
    Route::get('contact', [PagesController::class, 'getContact']);
    Route::post('contact', [PagesController::class, 'postContact']);
    Route::get('acknowledgements', [PagesController::class, 'getAcknowledgements']);
    Route::get('users', [AdminController::class, 'getUsers']);
    Route::get('users/{op}/{role}/{email}/{flag}/{name?}', [AdminController::class, 'getUpdateUsers']);
    Route::get('/evaluation/{date}', [AdminController::class, 'getEvaluation']);
    Route::get('milestones', [AdminController::class, 'getMilestones']);
    Route::post('milestones/uploadConfirm', [AdminController::class, 'getUploadedMilestones']);
    Route::post('milestones/uploadUpdate', [AdminController::class, 'uploadMilestones']);
    Route::post('milestones/{op}/{flag}/{id?}/{abbr_name?}/{full_name?}/{detail?}', [
        AdminController::class,
        'getUpdateMilestone',
    ]);
    Route::get('milestones/{op}/{flag}/{id?}/{abbr_name?}/{full_name?}/{detail?}', [
        AdminController::class,
        'getUpdateMilestone',
    ]);
    Route::get('db/{table}', [DBEditorController::class, 'viewPage']);
    Route::get('schedules', [AdminController::class, 'getSchedules']);
    Route::post('updateDB', [AdminController::class, 'postUpdateDB']);
    Route::post('addDB', [AdminController::class, 'postAddDB']);
    Route::post('editDB', [AdminController::class, 'postEditDB']);
    Route::get('resetTickets', [AdminController::class, 'resetTickets']);
    Route::post('updateTickets', [AdminController::class, 'postUpdateTickets']);
    Route::get('download', [AdminController::class, 'getDownload']);
    Route::get('evaluation', [AdminController::class, 'getEvaluation']);
    Route::get('uploadForm', [AdminController::class, 'uploadForm']);
    Route::post('upload', [AdminController::class, 'uploadFormPost']);
    Route::get('update_schedule_data', [AdminController::class, 'updateScheduleData']);
    Route::get('medhubtest', [MedhubController::class, 'medhubConnect']);
});

/**
 * Pre-surgery and post-surgery feedback page
 *
 * Authorization: residents and attendings
 */
Route::prefix('survey')->group(function () {
    Route::get('{date}', [PagesController::class, 'getFeedback']);
});
