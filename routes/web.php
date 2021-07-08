<?php

use Illuminate\Support\Facades\Auth;
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


Route::get('/', '\App\Http\Controllers\MainController@auth');

Route::group(['middleware' => ['auth', 'web']], function() {
    Route::post('/message','\App\Http\Controllers\MessageController@new_message');
    Route::get('/new_chat','\App\Http\Controllers\MessageController@new_chat');
    Route::get('/load_chats','\App\Http\Controllers\MessageController@load_chats');
    Route::get('/event_delete', '\App\Http\Controllers\EventController@delete');
    Route::get('/event_change', '\App\Http\Controllers\EventController@change');
    Route::get('/event_create', '\App\Http\Controllers\EventController@create');
    Route::get('/event_take', '\App\Http\Controllers\EventController@take');
    Route::get('/map_take', '\App\Http\Controllers\MapController@take');
    Route::get('/map_delete', '\App\Http\Controllers\MapController@delete');
    Route::get('/map_create', '\App\Http\Controllers\MapController@create');
    Route::get('/calc','\App\Http\Controllers\CalcController@calc')->name('calc');
    Route::get('/marks','\App\Http\Controllers\CalcController@marks')->name('marks');
    Route::get('/models','\App\Http\Controllers\CalcController@models')->name('models');

});

Route::name('user.')->group(function(){
    Route::post('/login','\App\Http\Controllers\AuthController@login');
    Route::post('/register','\App\Http\Controllers\AuthController@register');
    Route::get('/logout','\App\Http\Controllers\AuthController@logout')->name('logout');
});
Route::get('/init','\App\Http\Controllers\AuthController@init')->name('init');



