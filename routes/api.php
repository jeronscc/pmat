<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\SaroController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\HonorariaFormController;
use App\Http\Controllers\ProcurementFormController;
use App\Http\Controllers\OtherExpenseFormController;


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

    // Fetch procurement data from the 'procurement' table
    $procurements = DB::connection('ilcdb')
        ->table('procurement')
        ->select('procurement_id', 'activity', 'saro_no', 'year', 'procurement_category') // Include procurement_category
        ->orderBy('procurement_id', 'desc');

    // If a specific saro_no is provided, filter by it
    if ($saroNo) {
        $procurements->where('saro_no', $saroNo);
    }

    // If a year is provided, filter by the 'year' field
    if ($year) {
        $procurements->where('year', '=', $year); // Adjust this based on your actual year field
    }

    // Execute the query to get procurement data
    $procurements = $procurements->get();

    // Fetch procurement form data (status, unit) for regular procurement
    $procurementForms = DB::connection('ilcdb')->table('procurement_form')->get();

    // Fetch honoraria form data (status, unit) for honoraria category procurements
    $honorariaForms = DB::connection('ilcdb')->table('honoraria_form')->get();

    $otherexpenseForms = DB::connection('ilcdb')->table('otherexpense_form')->get();

    // Merge procurement data with form data
    $mergedData = $procurements->map(function ($procurement) use ($procurementForms, $honorariaForms, $otherexpenseForms) {

        // Debug: Check procurement data
        Log::info("Procurement Data: " . json_encode($procurement));
    
        // Try fetching the corresponding form for honoraria or procurement
        $form = $honorariaForms->firstWhere(function($item) use ($procurement) {
            return (string)$item->procurement_id === (string)$procurement->procurement_id;
        }) ?? $procurementForms->firstWhere(function($item) use ($procurement) {
            return (string)$item->procurement_id === (string)$procurement->procurement_id;
        }) ?? $otherexpenseForms->firstWhere(function($item) use ($procurement) {
            return (string)$item->procurement_id === (string)$procurement->procurement_id;
        });
    
        // Log form data to debug
        Log::info("Form Data: " . json_encode($form));
    
        // Return the merged data (procurement info + form info)
        return [
            'procurement_id' => $procurement->procurement_id,
            'activity' => $procurement->activity,
            'status' => $form ? $form->status : 'No Status', // If no form, return 'No Status'
            'unit' => $form ? $form->unit : 'No Unit', // If no form, return 'No Unit'
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
Route::post('/procurement/update', [ProcurementFormController::class, 'update']);
Route::get('/fetch-combined-procurement', [ProcurementController::class, 'fetchCombinedProcurementData']);
Route::post('/honoraria/update', [HonorariaFormController::class, 'updateHonoraria']);
Route::post('/otherexpense/update', [OtherExpenseFormController::class, 'updateOtherExpense']);