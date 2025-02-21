<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
use App\Http\Controllers\HonorariaController;
use App\Http\Controllers\AddSaroController;
use App\Http\Controllers\SaroController;
use App\Http\Middleware\PreventBackAfterLogout;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/homepage', function () {
    return view('homepage'); 
})->middleware('auth');

Route::get('/projectClick', function () {
    return view('projectClick'); 
})->middleware('auth');

Route::get('/dtc', function () {
    return view('dtc');
})->middleware('auth');

Route::get('/spark', function () {
    return view('spark');
})->middleware('auth');

Route::get('/honoraria-form', [HonorariaController::class, 'showHonorariaForm']);

Route::get('/procurementform', function () {
    return view('procurementform');
});
Route::post('/login', [userController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::post('/add-saro', [AddSaroController::class, 'addSaro'])->name('addSaro');
Route::post('/add-saro', [SaroController::class, 'store']);
//PREVENT BACK AFTER LOGOUT
Route::middleware(['auth', PreventBackAfterLogout::class])->group(function () {
    Route::get('/homepage', function () {
        return view('homepage');
    });
});

Route::middleware(['auth', PreventBackAfterLogout::class])->group(function () {
    Route::get('/dtc', function () {
        return view('dtc');
    });
});

Route::middleware(['auth', PreventBackAfterLogout::class])->group(function () {
    Route::get('/projectClick', function () {
        return view('projectClick');
    });
});

Route::middleware(['auth', PreventBackAfterLogout::class])->group(function () {
    Route::get('/spark', function () {
        return view('spark');
    });
});