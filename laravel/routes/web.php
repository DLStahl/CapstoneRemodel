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
    Route::get('', 'PagesController@getIndex');
    Route::get('about', 'PagesController@getAbout');
    Route::get('contact','PagesController@getContact');
    Route::post('contact', 'PagesController@postContact');
    Route::get('test','PagesController@test');
	Route::get('acknowledgements', 'PagesController@getAcknowledgements');
});

/**
 * Resident's page
 */
Route::group(['prefix' => 'resident', 'middleware' => 'resident'], function () {
    Route::get('/', 'ResidentController@getIndex');
    Route::get('home', 'PagesController@getIndex');
    Route::get('about', 'PagesController@getAbout');
    Route::get('contact','PagesController@getContact');
    Route::post('contact', 'PagesController@postContact');
	Route::get('acknowledgements', 'PagesController@getAcknowledgements');

    Route::get('schedule', 'ResidentController@getSchedule');
    Route::get('schedule/firstday', 'ScheduleDataController@getFirstDay');
    Route::get('schedule/secondday','ScheduleDataController@getSecondDay');
    Route::get('schedule/thirdday','ScheduleDataController@getThirdDay');

    Route::get('schedule/firstday/filter/{room}/{leadSurgeon}/{patient_class}/{starttime_endtime}', 'ScheduleDataController@getFirstDay');
    Route::get('schedule/secondday/filter/{room}/{leadSurgeon}/{patient_class}/{starttime_endtime}','ScheduleDataController@getSecondDay');
    Route::get('schedule/thirdday/filter/{room}/{leadSurgeon}/{patient_class}/{starttime_endtime}','ScheduleDataController@getThirdDay');

    Route::post('schedule/secondday/{id}', 'ScheduleDataController@getChoice');
	Route::post('schedule/thirdday/{id}', 'ScheduleDataController@getChoice');
	Route::get('schedule/secondday/milestones/{id}', 'ScheduleDataController@selectMilestones');
	Route::get('schedule/thirdday/milestones/{id}', 'ScheduleDataController@selectMilestones');
	Route::get('schedule/secondday/preferences/clear/{date}', 'ScheduleDataController@clearOption');
	Route::get('schedule/thirdday/preferences/clear/{date}', 'ScheduleDataController@clearOption');

    Route::post('schedule/submit', 'ScheduleDataController@postSubmit');

    Route::get('instructions','ResidentController@getInstructions');
});



/**
 * Admin's page
 */
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::get('/', 'AdminController@getIndex');
    Route::get('home', 'PagesController@getIndex');
    Route::get('about', 'PagesController@getAbout');
    Route::get('contact','PagesController@getContact');
    Route::post('contact', 'PagesController@postContact');
	Route::get('acknowledgements', 'PagesController@getAcknowledgements');

    Route::get('users', 'AdminController@getUsers');
    Route::get('users/{op}/{role}/{email}/{flag}/{name?}', 'AdminController@getUpdateUsers');
	Route::get('/evaluation/{date}', 'AdminController@getEvaluation');

    Route::get('schedules', 'AdminController@getSchedules');
    Route::post('updateDB', 'AdminController@postUpdateDB');
    Route::post('addDB', 'AdminController@postAddDB');
    Route::post('editDB', 'AdminController@postEditDB');
    Route::get('resetTickets', 'AdminController@resetTickets');
    Route::post('updateTickets', 'AdminController@postUpdateTickets');
    Route::get('postmessage', 'AdminController@getMessages');
    Route::get('download', 'AdminController@getDownload');
    Route::get('evaluation', 'AdminController@getEvaluation');
	Route::get('uploadForm', 'AdminController@uploadForm');
	Route::post('upload', 'AdminController@uploadFormPost');


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
Route::prefix('survey')->group(function() {
    Route::get('{date}', 'PagesController@getFeedback');
});
