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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('photos', 'PhotoController');

Route::any('/review', 'ReviewController@index');
Route::any('/review/getTotal', 'ReviewController@getTotal');
Route::post('/review/submitReview', 'ReviewController@submitReview');
Route::any('/review/getReviews', 'ReviewController@getReviews');
Route::get('/review/image/{path}', 'UploadController@image');

Route::any('/upload', 'UploadController@index');
