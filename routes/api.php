<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//ILCDB FETCH SARO
Route::get('/fetch-saro-ilcdb', function () {
    // Fetch data from the saro table in the ilcdb database
    $data = DB::connection('ilcdb')->table('saro')->get();

    // Return data as JSON
    return response()->json($data);
});

// ILCDB FETCH HONORARIACHECKLIST
Route::get('/fetch-honorariachecklist', function () {
    // Fetch data from the honorariachecklist table in the ilcdb database
    $data = DB::connection('requirements')->table('honorariachecklist')->get();

    // Return data as JSON
    return response()->json($data);
});