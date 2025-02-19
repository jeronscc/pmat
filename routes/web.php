<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
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

Route::get('/honorariaform', function () {
    $checklistItems = DB::connection('requirements')->table('honorariachecklist')->get();
    return view('honorariaform', ['checklistItems' => $checklistItems]);
})->middleware('auth');

Route::get('/procurementform', function () {
    return view('procurementform');
});
Route::post('/login', [userController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

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