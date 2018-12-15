<?php

Route::get('/', 'UserController@getTopPage');
Route::get('/signup',  'UserController@getSignUp');
Route::post('/usercrate', 'UserController@userCrate');
Route::post('/login', 'UserController@userLogin');
Route::get('/logoff', 'UserController@userLogoff');

Route::get('/search', 'SurveyController@webGetSurveyList');
Route::post('/search', 'SurveyController@webTextSearch');
Route::get('/api/search', 'SurveyController@apiGetSurveyList');

Route::get('/survey', 'SurveyController@webGetSurvey');
Route::get('/api/survey', 'SurveyController@apiGetSurvey');

//Route::get('/surveycrate', 'SurveyController@webGetSurvey');

Route::get('/surveycreate', 'SurveyController@webGetSurveyCreateForm');
Route::post('/surveycreate', 'SurveyController@webSurveyCreate');
Route::post('/api/surveycreate', 'SurveyController@apiSurveyCreate');


Route::post('/tagcreate', 'SurveyController@webCreateTag');
Route::post('/api/tagcreate', 'SurveyController@apiCreateTag');

Route::post('/tagerase', 'SurveyController@webEraseTag');
Route::post('/api/tagerase', 'SurveyController@apiEraseTag');

Route::post('/vote', 'SurveyController@webVote');
Route::post('/api/vote', 'SurveyController@apiVote');

Route::get('/debug', 'SurveyController@getDebugView');

//Auth認証に使うRoute設定。
//https://teratail.com/questions/106720
//Auth::routes();
//Route::get('/home', 'HomeController@index')->name('home');

//twitter
Route::get('/login/twitter', 'Auth\SocialController@getTwitterAuth');
Route::get('/login/twitter/callback', 'Auth\SocialController@getTwitterAuthCallback');
