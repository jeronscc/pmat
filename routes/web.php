<?php

use App\Http\Controllers\userController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/homepage', function () {
    return view('homepage'); 
});

Route::get('/projectClick', function () {
    return view('projectClick'); 
});

Route::get('/dtc', function () {
    return view('dtc');
});

Route::get('/spark', function () {
    return view('spark');
});

Route::get('/procurementform', function () {
    return view('procurementform');
});
Route::post('/login', [userController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');