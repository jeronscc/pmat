<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SaroController;
use App\Http\Controllers\ProcurementController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//ILCDB FETCH SARO
Route::get('/fetch-saro-ilcdb', function (Request $request) {
    $year = $request->query('year');

    if ($year) {
        // Fetch SARO data for a specific year and order by saro_no in descending order
        $data = DB::connection('ilcdb')
                    ->table('saro')
                    ->whereYear('year', $year)
                    ->select('saro_no', 'current_budget', 'year')
                    ->orderBy('saro_no', 'desc')  
                    ->get();
    } else {
        // Fetch all SARO data and order by saro_no in descending order
        $data = DB::connection('ilcdb')
                    ->table('saro')
                    ->select('saro_no', 'current_budget', 'year')  
                    ->orderBy('saro_no', 'desc')  
                    ->get();
    }
    // Return the data as JSON
    return response()->json($data);
});
// ILCDB FETCH SARO AND PROCUREMENT DATA
Route::get('/fetch-saro-ilcdb', [SaroController::class, 'fetchSaroData'])->name('fetchSaroData');
Route::get('/fetch-procurement-ilcdb', [ProcurementController::class, 'fetchProcurementData'])->name('fetchProcurementData');
Route::get('/search-procurement-ilcdb', function (Request $request) {
    $query = $request->query('query');

    $procurements = DB::connection('ilcdb')
        ->table('procurement')
        ->select('procurement_id', 'activity')
        ->where('procurement_id', 'like', "%{$query}%")
        ->orWhere('activity', 'like', "%{$query}%")
        ->orderBy('procurement_id', 'desc')
        ->get();

    return response()->json($procurements);
});
// ILCDB FETCH for FORM
Route::get('/fetch-procurement-ilcdb', [ProcurementController::class, 'fetchProcurementData'])->name('fetchProcurementData');
Route::post('/update-requirement/{id}', [ProcurementController::class, 'updateRequirement'])->name('updateRequirement');

// ILCDB FETCH HONORARIACHECKLIST
Route::get('/fetch-honorariachecklist', function () {
    // Fetch data from the honorariachecklist table in the ilcdb database
    $checklistItems = DB::connection('requirements')->table('honorariachecklist')->get();

    // Return data as JSON
    return response()->json($checklistItems);
});

// Fetch requirements for a specific SARO or all requirements if no saro_no is provided
Route::get('/fetch-procurement-ilcdb', function (Request $request) {
    $saroNo = $request->query('saro_no'); // Get the saro_no from the request
    $year = $request->query('year'); // Get the year from the request

    $query = DB::connection('ilcdb')
        ->table('procurement')
        ->select('procurement_id', 'activity')
        ->orderBy('procurement_id', 'desc');

    // If a specific saro_no is provided, filter by it
    if ($saroNo) {
        $query->where('saro_no', $saroNo);
    }

    // If a year is provided, filter by the 'year' field
    if ($year) {
        $query->where('year', '=', $year); // Adjust this based on your actual year field
    }

    // Execute the query
    $procurement = $query->get();

    // Return the requirements as JSON
    return response()->json($procurement);
});

//SEARCH PROCUREMENTS
Route::get('/search-procurement-ilcdb', function (Request $request) {
    $query = $request->query('query');

    $procurements = DB::connection('ilcdb')
        ->table('procurement')
        ->select('procurement_id', 'activity')
        ->where('procurement_id', 'like', "%{$query}%")
        ->orWhere('activity', 'like', "%{$query}%")
        ->orderBy('procurement_id', 'desc')
        ->get();

    return response()->json($procurements);
});

// ILCDB POST REQUESTS
Route::any('/add-saro-ilcdb', [SaroController::class, 'addSaro'])->name('add-saro-ilcdb');
Route::any('/add-procurement-ilcdb', [ProcurementController::class, 'addProcurement'])->name('addProcurement');

