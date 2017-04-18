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
    Route::get('province/lgu', 'ProvinceController@fetch');
    Route::post('user', 'UserController@insert');
    Route::get('user/management', 'UserController@fetch');
    Route::post('program', 'ProgramController@insert');
    Route::get('program/management', 'ProgramController@fetch');
    Route::get('program/stats/{id}', 'ProgramStatsController@programView');
    Route::get('program/stats', 'ProgramStatsController@fetchAll');
    Route::post('program/stats', 'ProgramStatsController@insert');
    Route::post('submitted/date', 'SubmittedDateController@insert');
    Route::get('submitted/date', 'SubmittedDateController@fetch');
    Route::get('report/files/{id}', 'ReportFileController@fetchByStatId');
    Route::get('report/file', 'ReportFileController@fetch');
    Route::post('upload/report/file', 'ReportFileController@upload');
    Route::post('download/report/file', 'ReportFileController@downloadFile');
    Route::get('programs/sortby/{program_name}/{type}', 'ProgramController@sortBy');
    Route::delete('program/stat/{id}', 'ProgramStatsController@delete');
    Route::get('report/files/view/{prog_id}/{stat_id}/{source}', 'ReportFileController@viewFile');
    Route::post('checked/lgu', 'CheckedLguController@findAndinsert');
    Route::post('fetch/checked/lgu', 'CheckedLguController@fetchByHeaders');
    Route::post('fetch_checked_lgu_by_program', 'CheckedLguController@fetchCheckedByProgram');
    Route::post('checkall_checked_lgu', 'CheckedLguController@checkOrUncheck');
    Route::post('remove_checked_lgu', 'CheckedLguController@remove');
    Route::post('delete_existing_checked_lgu', 'CheckedLguController@deleteExisting');
    Route::delete('program/{id}', 'ProgramController@delete');
    Route::get('checked_lgu_fetch_all', 'CheckedLguController@fetchAll');
    Route::post('create_checked_lgu', 'CheckedLguController@addNewCheckedLgu');
    Route::get('update_brgys', 'BrgyController@updateBrgys');
    Route::delete('program_stat/{id}', 'ProgramStatsController@delete');
    Route::get('brgy', 'BrgyController@fetch');
    Route::post('fetch_checked_brgy', 'CheckedBrgyController@insertOrDelete');
    Route::post('check_brgy_fetch', 'CheckedBrgyController@fetchByLgu');
    Route::get('fetch_all_checked_brgys', 'CheckedBrgyController@fetchAll');
    Route::post('check_of_uncheck_all_checked_brgys', 'CheckedBrgyController@checkUncheckAll');
});
