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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/tasks', 'TaskController@index')->middleware('auth')->name('tasks');


//Bro, I didn't want to use tokens
Route::middleware(['auth'])->prefix('api')->group(function () {

    Route::resource('tasks', 'Api\TaskController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::get('/tasks', 'Api\TaskController@index');
    Route::post('/tasks', 'Api\TaskController@store');
    Route::get('/tasks/{task:id}', 'Api\TaskController@update')->middleware('can:update,task');
    Route::delete('/tasks/{task:id}', 'Api\TaskController@destroy')->middleware('can:delete,task');




});
