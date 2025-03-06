<?php

use App\Http\Controllers\HonorariaFormController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SaroController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\ProcurementFormController;


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

    $procurements = DB::connection('ilcdb')
        ->table('procurement')
        ->select('procurement_id', 'activity', 'saro_no', 'year') // Select necessary fields from procurement table
        ->orderBy('procurement_id', 'desc');

    // If a specific saro_no is provided, filter by it
    if ($saroNo) {
        $procurements->where('saro_no', $saroNo);
    }

    // If a year is provided, filter by the 'year' field
    if ($year) {
        $procurements->where('year', '=', $year); // Adjust this based on your actual year field
    }

    // Execute the query for procurement data
    $procurements = $procurements->get();

    // Now, we need to fetch the status from the 'procurement_form' table
    $procurementForms = DB::connection('ilcdb')->table('procurement_form')->get();

    // Combine procurement data with procurement form status
    $mergedData = $procurements->map(function ($procurement) use ($procurementForms) {
        // Find the matching procurement form by procurement_id
        $form = $procurementForms->firstWhere('procurement_id', $procurement->procurement_id);

        // Return combined data
        return [
            'procurement_id' => $procurement->procurement_id,
            'activity' => $procurement->activity,
            'status' => $form ? $form->status : 'No Status', // Default to 'No Status' if no matching form
            'unit' => $form ? $form->unit : 'No Unit', // Default to 'No Unit' if no matching form
        ];
    });

    // Return the merged data as a JSON response
    return response()->json($mergedData);
});

Route::get('/fetch-honoraria-ilcdb', function (Request $request) {
    $saroNo = $request->query('saro_no'); // Get the saro_no from the request
    $year = $request->query('year'); // Get the year from the request

    $procurements = DB::connection('ilcdb')
        ->table('procurement')
        ->select('procurement_id', 'activity', 'saro_no', 'year') // Select necessary fields from procurement table
        ->orderBy('procurement_id', 'desc');

    // If a specific saro_no is provided, filter by it
    if ($saroNo) {
        $procurements->where('saro_no', $saroNo);
    }

    // If a year is provided, filter by the 'year' field
    if ($year) {
        $procurements->where('year', '=', $year); // Adjust this based on your actual year field
    }

    // Execute the query for procurement data
    $procurements = $procurements->get();

    // Now, we need to fetch the status from the 'procurement_form' table
    $honorariaForm = DB::connection('ilcdb')->table('honoraria_form')->get();

    // Combine procurement data with procurement form status
    $mergedData = $procurements->map(function ($procurement) use ($honorariaForm) {
        // Find the matching procurement form by procurement_id
        $form = $honorariaForm->firstWhere('procurement_id', $procurement->procurement_id);

        // Return combined data
        return [
            'procurement_id' => $procurement->procurement_id,
            'activity' => $procurement->activity,
            'status' => $form ? $form->status : 'No Status', // Default to 'No Status' if no matching form
            'unit' => $form ? $form->unit : 'No Unit', // Default to 'No Unit' if no matching form
        ];
    });

    // Return the merged data as a JSON response
    return response()->json($mergedData);
});

//SEARCH PROCUREMENTS
Route::get('/search-procurement-ilcdb', function (Request $request) {
    $query = $request->query('query');

    // Check if query exists before proceeding
    if (!$query) {
        return response()->json([], 400); // Return an empty array if no query provided
    }

    // Perform the search using the provided query parameter
    $procurements = DB::connection('ilcdb')
        ->table('procurement')  // Start from procurement table
        ->join('procurement_form', 'procurement.procurement_id', '=', 'procurement_form.procurement_id')  // Join procurement_form table on procurement_id
        ->select('procurement.procurement_id', 'procurement.activity', 'procurement_form.status', 'procurement_form.unit')  // Select columns from both tables
        ->where('procurement.procurement_id', 'like', "%{$query}%")  // Search in procurement_id
        ->orWhere('procurement.activity', 'like', "%{$query}%")  // Search in activity
        ->orderBy('procurement.procurement_id', 'desc')  // Order by procurement_id
        ->get();  // Get the result

    return response()->json($procurements);  // Return the results as JSON
});

// ILCDB POST REQUESTS
Route::any('/add-saro-ilcdb', [SaroController::class, 'addSaro'])->name('add-saro-ilcdb');
Route::any('/add-procurement-ilcdb', [ProcurementController::class, 'addProcurement'])->name('addProcurement');
Route::get('/fetch-procurement-details', [ProcurementController::class, 'fetchProcurementDetails'])->name('fetchProcurementDetails');
Route::get('/fetch-honoraria-details', [ProcurementController::class, 'fetchProcurementDetails'])->name('fetchProcurementDetails');
Route::post('/procurement/update', [ProcurementFormController::class, 'update']);
Route::get('/fetch-combined-procurement', [ProcurementController::class, 'fetchCombinedProcurementData']);
Route::post('/honoraria/update', [HonorariaFormController::class, 'updateHonoraria']);
