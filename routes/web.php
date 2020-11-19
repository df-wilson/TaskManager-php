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

Route::post('api/task', 'TodoController@store');
Route::get('api/task', 'TodoController@all');
Route::delete('api/todo/{id}', 'TodoController@delete');
Route::put('api/task/priority/{id}', 'TodoController@updatePriority');
Route::put('api/task/status/{id}', 'TodoController@updateStatus');
Route::put('api/task/due/{id}', 'TodoController@updateDueDate');

