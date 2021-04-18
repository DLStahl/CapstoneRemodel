<?php

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

/**
 * Home page
 */
Route::prefix('/')->group(function () {
    Route::get('', function () {
        return redirect('/resident/schedule/secondday');
    });
    Route::get('about', 'PagesController@getAbout');
    Route::get('contact', 'PagesController@getContact');
    Route::post('contact', 'PagesController@postContact');
    Route::get('acknowledgements', 'PagesController@getAcknowledgements');
});

/**
 * Resident's page
 */
Route::group(['prefix' => 'resident', 'middleware' => 'resident'], function () {
    Route::get('/', 'ResidentController@getIndex');
    Route::get('home', 'PagesController@getIndex');
    Route::get('about', 'PagesController@getAbout');
    Route::get('contact', 'PagesController@getContact');
    Route::post('contact', 'PagesController@postContact');
    Route::get('acknowledgements', 'PagesController@getAcknowledgements');

    Route::get('schedule', 'ResidentController@getSchedule');

    Route::get('schedule/{day}', 'ScheduleDataController@getDay');

    Route::get(
        'schedule/{day}/filter/{room}/{leadSurgeon}/{rotation}/{starttime_endtime}',
        'ScheduleDataController@getDay'
    );

    Route::post('schedule/confirm', 'ScheduleDataController@getChoice');
    Route::get('schedule/milestones/{id}', 'ScheduleDataController@selectMilestones');
    Route::get('schedule/milestonesEdit/{id}', 'ScheduleDataController@updateMilestones');
    Route::get('schedule/secondday/preferences/clear/{date}', 'ScheduleDataController@clearOption');
    Route::get('schedule/thirdday/preferences/clear/{date}', 'ScheduleDataController@clearOption');

    Route::post('schedule/submit', 'ScheduleDataController@postSubmit');

    Route::get('instructions', 'ResidentController@getInstructions');

    Route::get('postmessage', 'ResidentController@getMessages');
    Route::post('announcement', 'PagesController@postAnnouncement');
    Route::post('deleteannouncement', 'PagesController@deleteAnnouncement');
});

/**
 * Admin's page
 */
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::get('/', 'AdminController@getIndex');
    Route::get('home', 'PagesController@getIndex');
    Route::get('about', 'PagesController@getAbout');
    Route::get('contact', 'PagesController@getContact');
    Route::post('contact', 'PagesController@postContact');
    Route::get('acknowledgements', 'PagesController@getAcknowledgements');

    Route::get('users', 'AdminController@getUsers');
    Route::get('users/{op}/{role}/{email}/{flag}/{name?}', 'AdminController@getUpdateUsers');
    Route::get('/evaluation/{date}', 'AdminController@getEvaluation');

    Route::get('milestones', 'AdminController@getMilestones');
    Route::post('milestones/uploadConfirm', 'AdminController@getUploadedMilestones');
    Route::post('milestones/uploadUpdate', 'AdminController@uploadMilestones');
    Route::post(
        'milestones/{op}/{flag}/{id?}/{abbr_name?}/{full_name?}/{detail?}',
        'AdminController@getUpdateMilestone'
    );
    Route::get(
        'milestones/{op}/{flag}/{id?}/{abbr_name?}/{full_name?}/{detail?}',
        'AdminController@getUpdateMilestone'
    );

    Route::get('db/{table}', 'admin\DBEditorController@viewPage');

    Route::get('schedules', 'AdminController@getSchedules');
    Route::post('updateDB', 'AdminController@postUpdateDB');
    Route::post('addDB', 'AdminController@postAddDB');
    Route::post('editDB', 'AdminController@postEditDB');
    Route::get('resetTickets', 'AdminController@resetTickets');
    Route::post('updateTickets', 'AdminController@postUpdateTickets');
    Route::get('download', 'AdminController@getDownload');
    Route::get('evaluation', 'AdminController@getEvaluation');
    Route::get('uploadForm', 'AdminController@uploadForm');
    Route::post('upload', 'AdminController@uploadFormPost');

    // webssh went down, needed to update schedule
    Route::get('update_schedule_data', 'AdminController@updateScheduleData');

    /**
     * Medhub
     */
    Route::get('medhubtest', 'MedhubController@medhubConnect');
});

/**
 * Pre-surgery and post-surgery feedback page
 *
 * Authorization: residents and attendings
 *
 */
Route::prefix('survey')->group(function () {
    Route::get('{date}', 'PagesController@getFeedback');
});
