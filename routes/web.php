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

Route::get('/', 'MainController@index');
Route::get('/r/{hash}', 'MainController@index');

Route::post('/request/send', 'MainController@request');
Route::get('/saved/get', 'MainController@getSavedRequests');
Route::get('/saved/{id}/get', 'MainController@getSavedRequest');
Route::post('/saved/{id}/store', 'MainController@storeSavedRequest');

Auth::routes();

Route::get('/home', 'MainController@index')->name('home');
