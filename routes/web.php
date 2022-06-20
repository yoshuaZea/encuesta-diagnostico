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

// Auth::routes();

// LOGIN
Route::get('/iniciar-sesion', 'Auth\LoginController@index')->name('login');
Route::post('/iniciar-sesion', 'Auth\LoginController@login')->name('login.post');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

// SURVEY
Route::get('/', 'EncuestaController@index')->name('survey.index');
Route::post('/', 'EncuestaController@store')->name('survey.store');
Route::get('/encuesta/{uuid}', 'EncuestaController@questions')->name('survey.questions');
Route::put('/encuesta/{uuid}', 'EncuestaController@update')->name('survey.update');
Route::get('/encuesta-completada', 'EncuestaController@finish')->name('survey.finish');

// REPORTS
Route::middleware(['auth'])->group(function () {
    Route::get('/reportes', 'EncuestaController@report')->name('reports.index');
    Route::post('/reportes', 'EncuestaController@download')->name('reports.download');
});


