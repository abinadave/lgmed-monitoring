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

Route::get('/', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::group(['middleware' => 'auth'], function () {
    Route::post('user', 'UserController@insert');
    Route::get('user/management', 'UserController@fetch');
    Route::post('program', 'ProgramController@insert');
    Route::get('program/management', 'ProgramController@fetch');
    Route::get('program/stats/{id}', 'ProgramStatsController@programView');
    Route::post('program/stats', 'ProgramStatsController@insert');
});
