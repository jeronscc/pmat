<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
use App\Http\Middleware\PreventBackAfterLogout;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProcurementFormController;
use App\Http\Controllers\HonorariaFormController;
use App\Http\Controllers\OtherExpenseFormController;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/homepage-ilcdb', function () {
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

Route::post('/login', [userController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

//REDIRECT TO FORM PAGES
Route::get('/procurementform', function () {
    return view('procurementform');
});

Route::get('/honorariaform', function () {
    return view('honorariaform');
});

Route::get('/otherexpenseform', function () {
    return view('otherexpenseform');
});

//PREVENT BACK AFTER LOGOUT
Route::middleware(['auth', PreventBackAfterLogout::class])->group(function () {
    Route::get('/homepage-ilcdb', function () {
        return view('homepage');
    });
});

Route::middleware(['auth', PreventBackAfterLogout::class])->group(function () {
    Route::get('/homepage-dtc', function () {
        return view('dtc');
    });
});

Route::middleware(['auth', PreventBackAfterLogout::class])->group(function () {
    Route::get('/homepage-projectClick', function () {
        return view('projectClick');
    });
});

Route::middleware(['auth', PreventBackAfterLogout::class])->group(function () {
    Route::get('/homepage-spark', function () {
        return view('spark');
    });
});


// REDIRECTS TO SVP FORM (PR ID SPECIFIED)
Route::get('/procurementform', [ProcurementFormController::class, 'showForm'])->name('procurementform');
Route::post('/procurementform/update', [ProcurementFormController::class, 'update'])->name('procurement.update');

Route::get('/honorariaform', [HonorariaFormController::class, 'showForm'])->name('honoraria.form');
Route::post('/honorariaform/update', [HonorariaFormController::class, 'updateHonoraria'])->name('honoraria.update');

Route::get('/otherexpenseform', [OtherExpenseFormController::class, 'showOtherExpenseForm'])->name('otherexpense.form'); 