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

Route::get('/', 'WebController@index');
Route::post('/upload', 'Controller@upload');
Route::get('/{filename}', 'Controller@getFile');
Route::get('/{filename}/is-cached', 'Controller@isFileCached');
Route::post('/{filename}/delete', 'Controller@deleteFile');