<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

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

    // If a specific saro_no is provided, filter by it, otherwise fetch all requirements
    $query = DB::connection('ilcdb')
        ->table('procurement')
        ->select('procurement_id', 'activity')
        ->orderBy('procurement_id', 'desc');
    
    if ($saroNo) {
        $query->where('saro_no', $saroNo); // Filter by saro_no if provided
    }

    // Execute the query
    $requirements = $query->get();

    // Return the requirements as JSON
    return response()->json($requirements);
});

