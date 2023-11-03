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

// website

Route::get('/', function () {return redirect('/dashboard');});

Route::get('/dashboard', function () {
    return view('welcome');
});

Route::get('/dashboard/{any?}', function ($any) {
    return view('welcome');
})->where('any', '.*');
