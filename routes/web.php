<?php

include("testing/mail.php");

Route::get('/', 'ScheduleController@showSchedule')->name('index');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// Home Route
Route::get('/home', 'ScheduleController@showSchedule')->name('home');

//Schedule Route
Route::get('/schedule', 'ScheduleController@showSchedule')->name('schedule');
Route::get('/getSchedules', 'ScheduleController@getSchedules');
Route::delete('/deleteSchedule', 'ScheduleController@deleteSchedule');
Route::delete('/schedule', 'ScheduleController@deleteSchedule')->name('deleteschedule');
Route::patch('/schedule', 'ScheduleController@updateSchedule');
Route::get('/schedulemgm', 'ScheduleController@showScheduleMgm')->name('schedulemgm');
Route::post('/schedule/order_check', 'ScheduleController@order_check');
Route::post('/schedule/order', 'ScheduleController@order');

//course Route
Route::post('/schedulemgm', 'ScheduleController@createCourse')->name('createCourse');
Route::delete('/schedulemgm', 'ScheduleController@deleteCourse')->name('deleteCourse');
Route::put('/pauseCourse', 'ScheduleController@pauseCourse')->name('pauseCourse');
Route::put('/resumeCourse', 'ScheduleController@resumeCourse')->name('resumeCourse');

//usermgm Route
Route::get('/usermanagement', 'Controller@showUserManagement')->name('usermanagement');
Route::post('/usermanagement', 'Controller@changeUserAdmin')->name('changeuseradmin');
Route::post('/updateUserPermission', 'Controller@updateUserPermission');

//band Route
Route::get('/band', 'Controller@showUserBands')->name('band');
Route::post('/band', 'BandController@createBand')->name('createband');
Route::delete('/band', 'Controller@deleteBand')->name('deleteband');
Route::get('bandlist', 'BandController@showBand')->name('bandlist');
Route::post('/band/join', 'BandController@joinBand')->name('joinBand');

//feedback Route
Route::get('/feedback', 'Controller@showFeedback')->name('feedback');
Route::post('/feedback', 'Controller@createFeedback')->name('createfeedback');
