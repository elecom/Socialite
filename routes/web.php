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

// SOCIALITE
Route::get('login/{provider}', [App\Http\Controllers\Auth\LoginController::class, 'redirectToProvider']);
Route::get('login/{provider}/callback', [App\Http\Controllers\Auth\LoginController::class, 'handlerProviderCallback']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
