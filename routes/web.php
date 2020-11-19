<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/home', 'HomeController@index')->name('home');

Route::post('api/task', 'TaskController@store');
Route::get('api/task', 'TaskController@all');
Route::delete('api/task/{id}', 'TaskController@delete');
Route::put('api/task/priority/{id}', 'TaskController@updatePriority');
Route::put('api/task/status/{id}', 'TaskController@updateStatus');
Route::put('api/task/due/{id}', 'TaskController@updateDueDate');

