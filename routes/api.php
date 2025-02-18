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