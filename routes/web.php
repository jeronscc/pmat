<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
use App\Http\Middleware\PreventBackAfterLogout;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProcurementFormController;
use App\Http\Controllers\HonorariaFormController;
use App\Http\Controllers\OtherExpenseFormController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\AccountController;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/select-project', function () {
    return view('selection');
})->name('select-project');

Route::get('/homepage-ilcdb', function () {
    return view('homepage'); 
})->middleware('auth');

Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts');
});

Route::get('/homepage-projectClick', function () {
    return view('projectClick'); 
})->middleware('auth');

Route::get('/homepage-dtc', function () {
    return view('dtc');
})->middleware('auth');

Route::get('/homepage-spark', function () {
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


// REDIRECTS TO FORMS (PR ID SPECIFIED)
Route::post('/accounts/add', [AccountController::class, 'store'])->name('accounts.add');
Route::get('/procurementform', [ProcurementFormController::class, 'showForm'])->name('procurementform');
Route::put('/accounts/update', [AccountController::class, 'update'])->name('accounts.update');
Route::delete('/accounts/delete/{user_id}', [AccountController::class, 'destroy'])->name('accounts.delete');

Route::post('/procurementform/update', [ProcurementFormController::class, 'update'])->name('procurement.update');

Route::get('/honorariaform', [HonorariaFormController::class, 'showForm'])->name('honoraria.form');
Route::post('/honorariaform/update', [HonorariaFormController::class, 'updateHonoraria'])->name('honoraria.update');

Route::get('/otherexpenseform', [OtherExpenseFormController::class, 'showForm'])->name('otherexpense.form');  
Route::post('/otherexpenseform/update', [OtherExpenseFormController::class, 'updateOtherExpense'])->name('otherexpense.update');

Route::get('/api/requirements/{procurement_id}/files', [HonorariaFormController::class, 'getUploadedFiles']);