<?php

Route::get('/', 'UserController@getTopPage');
Route::get('/signup',  'UserController@getSignUp');
Route::post('/usercrate', 'UserController@userCrate');
Route::post('/login', 'UserController@userLogin');
Route::get('/logoff', 'UserController@userLogoff');

Route::get('/search', 'SurveyController@getSurveyList');
Route::post('/search', 'SurveyController@textSearch');

Route::get('/survey', 'SurveyController@getSurvey');

//Route::get('/surveycrate', 'SurveyController@getSurvey');

Route::get('/surveycreate', 'SurveyController@getSurveyCreateForm');
Route::post('/surveycreate', 'SurveyController@surveyCreate');

Route::post('/tagcreate', 'SurveyController@createTag');
Route::post('/tagerase', 'SurveyController@eraseTag');

Route::post('/vote', 'SurveyController@vote');
