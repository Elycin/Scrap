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

Route::get('/', 'Controller@home')->name('home');

// Uploading routes
Route::post('/upload', 'UploadController@upload');
Route::get('/{filename}', 'UploadController@getFile');
Route::get('/{filename}/is-cached', 'UploadController@isFileCached');
Route::post('/{filename}/delete', 'UploadController@deleteFile');