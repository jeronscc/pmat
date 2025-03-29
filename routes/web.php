<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
use App\Http\Middleware\PreventBackAfterLogout;
use Illuminate\Support\Facades\DB;

//ILCDB Web Routes
use App\Http\Controllers\ilcdbController\ProcurementController as IlcdbProcurementController;
use App\Http\Controllers\ilcdbController\HonorariaFormController as IlcdbHonorariaFormController;
use App\Http\Controllers\ilcdbController\ProcurementFormController as IlcdbProcurementFormController;
use App\Http\Controllers\ilcdbController\OtherExpenseFormController as IlcdbOtherExpenseFormController;

use App\Http\Controllers\dtcController\HonorariaFormController as dtcHonorariaFormController;
use App\Http\Controllers\dtcController\ProcurementFormController as dtcProcurementFormController;
use App\Http\Controllers\dtcController\OtherExpenseFormController as dtcOtherExpenseFormController;
use App\Http\Controllers\AccountController;
use App\Http\Middleware\AdminMiddleware;


// ISOLATED PAGES

Route::get('/', function () {
    return view('index');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/select-project', function () {
    return view('selection');
})->name('select-project');

Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts');
});


// PROJECT PAGES

Route::get('/homepage-ilcdb', function () {
    return view('homepage'); 
})->middleware('auth');

Route::get('/homepage-projectClick', function () {
    return view('projectClick'); 
})->middleware('auth');

Route::get('/homepage-dtc', function () {
    return view('dtc');
})->middleware('auth');

Route::get('/homepage-spark', function () {
    return view('spark');
})->middleware('auth');

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

// LOGIN LOGOUT
Route::post('/login', [userController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');


// FOR ILCDB
//REDIRECT TO ILCDB FORM PAGES
Route::get('/procurementform', function () {
    return view('procurementform');
});

Route::get('/honorariaform', function () {
    return view('honorariaform');
});

Route::get('/otherexpenseform', function () {
    return view('otherexpenseform');
});

Route::get('/dtcHonoraria', function () {
    return view('dtcHonoraria');
});

Route::get('/dtcSVP', function () {
    return view('dtcSVP');
});

Route::get('/dtcDTE', function () {
    return view('dtcDTE');
});

Route::get('/dtcSVP', [dtcProcurementFormController::class, 'showForm'])->name('procurementform');
Route::post('/dtcSVP/update', [dtcProcurementFormController::class, 'update'])->name('procurement.update');

Route::get('/dtcHonoraria', [dtcHonorariaFormController::class, 'showForm'])->name('honoraria.form');
Route::post('/dtcHonoraria/update', [dtcHonorariaFormController::class, 'updateHonoraria'])->name('honoraria.update');

Route::get('/dtcDTE', [dtcOtherExpenseFormController::class, 'showForm'])->name('otherexpense.form');  
Route::post('/dtcDTE/update', [dtcOtherExpenseFormController::class, 'updateOtherExpense'])->name('otherexpense.update');
// REDIRECTS TO ILCDB APIs

Route::post('/accounts/add', [AccountController::class, 'store'])->name('accounts.add');
Route::get('/procurementform', [IlcdbProcurementFormController::class, 'showForm'])->name('procurementform');
Route::put('/accounts/update', [AccountController::class, 'update'])->name('accounts.update');
Route::delete('/accounts/delete/{user_id}', [AccountController::class, 'destroy'])->name('accounts.delete');

Route::post('/procurementform/update', [IlcdbProcurementFormController::class, 'update'])->name('procurement.update');

Route::get('/honorariaform', [IlcdbHonorariaFormController::class, 'showForm'])->name('honoraria.form');
Route::post('/honorariaform/update', [IlcdbHonorariaFormController::class, 'updateHonoraria'])->name('honoraria.update');

Route::get('/otherexpenseform', [IlcdbOtherExpenseFormController::class, 'showForm'])->name('otherexpense.form');  
Route::post('/otherexpenseform/update', [IlcdbOtherExpenseFormController::class, 'updateOtherExpense'])->name('otherexpense.update');

Route::get('/api/requirements/{procurement_id}/files', [IlcdbHonorariaFormController::class, 'getUploadedFiles']);


// FOR DTC