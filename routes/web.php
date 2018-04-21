<?php

Route::get('/', 'UserController@getTopPage');
Route::get('/signup',  'UserController@getSignUp');
Route::post('/usercrate', 'UserController@userCrate');
Route::post('/login', 'UserController@userLogin');
Route::post('/logoff', 'UserController@userLogoff');