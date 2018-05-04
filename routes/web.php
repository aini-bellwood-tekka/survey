<?php

Route::get('/', 'UserController@getTopPage');
Route::get('/signup',  'UserController@getSignUp');
Route::post('/usercrate', 'UserController@userCrate');
Route::post('/login', 'UserController@userLogin');
Route::get('/logoff', 'UserController@userLogoff');

Route::get('/search', 'SurveyController@getSurveyList');

Route::get('/survey', 'SurveyController@getSurvey');

//Route::get('/surveycrate', 'SurveyController@getSurvey');

Route::get('/surveycreate', 'SurveyController@getSurveyCreateForm');
Route::post('/surveycreate', 'SurveyController@surveyCreate');

Route::post('/vote', 'SurveyController@vote');
