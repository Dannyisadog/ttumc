<?php
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
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

// Home Route
Route::get('/home', 'ScheduleController@showSchedule')->name('home');

//Schedule Route
Route::get('/schedule', 'ScheduleController@showSchedule')->name('schedule');
Route::post('/schedule', 'ScheduleController@createSchedule')->name('createschedule');
Route::delete('/schedule', 'ScheduleController@deleteSchedule')->name('deleteschedule');
Route::patch('/schedule', 'ScheduleController@updateSchedule');
Route::get('/schedulemgm', 'ScheduleController@showScheduleMgm')->name('schedulemgm');

//course Route
Route::post('/schedulemgm', 'ScheduleController@createCourse')->name('createCourse');
Route::delete('/schedulemgm', 'ScheduleController@deleteCourse')->name('deleteCourse');
Route::put('/pauseCourse', 'ScheduleController@pauseCourse')->name('pauseCourse');
Route::put('/resumeCourse', 'ScheduleController@resumeCourse')->name('resumeCourse');

//usermgm Route
Route::get('/usermanagement', 'Controller@showUserManagement')->name('usermanagement');
Route::post('/usermanagement', 'Controller@changeUserAdmin')->name('changeuseradmin');

//band Route
Route::get('/band', 'Controller@showBand')->name('band');
Route::post('/band', 'Controller@createBand')->name('createband');
Route::delete('/band', 'Controller@deleteBand')->name('deleteband');
Route::get('bandmanagement', 'Controller@showBandManagement')->name('bandmanagement');

//feedback Route
Route::get('/feedback', 'Controller@showFeedback')->name('feedback');
Route::post('/feedback', 'Controller@createFeedback')->name('createfeedback');