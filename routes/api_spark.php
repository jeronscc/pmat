<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\sparkController\SaroController as sparkSaroController;
use App\Http\Controllers\sparkController\ProcurementController as sparkProcurementController;
use App\Http\Controllers\sparkController\HonorariaFormController as sparkHonorariaFormController;
use App\Http\Controllers\sparkController\ProcurementFormController as sparkProcurementFormController;
use App\Http\Controllers\sparkController\OtherExpenseFormController as sparkOtherExpenseFormController;

//FOR spark
Route::prefix('spark')->middleware('api')->group(function () {
    //spark FETCH SARO
    Route::get('/fetch-saro-spark', function (Request $request) {
        $year = $request->query('year');

        if ($year) {
            // Fetch SARO data for a specific year and order by saro_no in descending order
            $data = DB::connection('spark')
                ->table('saro')
                ->whereYear('year', $year)
                ->select('saro_no', 'current_budget', 'year')
                ->orderBy('saro_no', 'desc')
                ->get();
        } else {
            // Fetch all SARO data and order by saro_no in descending order
            $data = DB::connection('spark')
                ->table('saro')
                ->select('saro_no', 'current_budget', 'year')
                ->orderBy('saro_no', 'desc')
                ->get();
        }
        // Return the data as JSON
        return response()->json($data);
    });


    // spark FETCH SARO AND PROCUREMENT DATA
    Route::get('/fetch-saro-spark', [sparkSaroController::class, 'fetchSaroData'])->name('fetchSaroData');
    Route::get('/fetch-procurement-spark', [sparkProcurementController::class, 'fetchProcurementData'])->name('fetchProcurementData');
    Route::get('/search-procurement-spark', function (Request $request) {
        $query = $request->query('query');

        $procurements = DB::connection('spark')
            ->table('procurement')
            ->select('procurement_id', 'activity', 'procurement_category')
            ->where('procurement_id', 'like', "%{$query}%")
            ->orWhere('activity', 'like', "%{$query}%")
            ->orWhere('procurement_category', 'like', "%{$query}%")
            ->orderBy('procurement_id', 'desc')
            ->get();

        return response()->json($procurements);
    });

    // spark FETCH HONORARIACHECKLIST
    Route::get('/fetch-honorariachecklist', function () {
        // Fetch data from the honorariachecklist table in the spark database
        $checklistItems = DB::connection('requirements')->table('honorariachecklist')->get();

        // Return data as JSON
        return response()->json($checklistItems);
    });

    // Fetch requirements for a specific SARO or all requirements if no saro_no is provided
    Route::get('/fetch-procurement-spark', function (Request $request) {
        $saroNo = $request->query('saro_no'); // Get the saro_no from the request
        $year = $request->query('year'); // Get the year from the request

        // Fetch procurement data from the 'procurement' table
        $procurements = DB::connection('spark')
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
        $procurementForms = DB::connection('spark')->table('procurement_form')->get();

        // Fetch honoraria form data (status, unit) for honoraria category procurements
        $honorariaForms = DB::connection('spark')->table('honoraria_form')->get();

        $otherexpenseForms = DB::connection('spark')->table('otherexpense_form')->get();

        // Merge procurement data with form data
        $mergedData = $procurements->map(function ($procurement) use ($procurementForms, $honorariaForms, $otherexpenseForms) {

            // Debug: Check procurement data
            Log::info("Procurement Data: " . json_encode($procurement));

            // Try fetching the corresponding form for honoraria or procurement
            $form = $honorariaForms->firstWhere(function ($item) use ($procurement) {
                return (string)$item->procurement_id === (string)$procurement->procurement_id;
            }) ?? $procurementForms->firstWhere(function ($item) use ($procurement) {
                return (string)$item->procurement_id === (string)$procurement->procurement_id;
            }) ?? $otherexpenseForms->firstWhere(function ($item) use ($procurement) {
                return (string)$item->procurement_id === (string)$procurement->procurement_id;
            });

            // Log form data to debug
            Log::info("Form Data: " . json_encode($form));

            // Return the merged data (procurement info + form info)
            return [
                'procurement_id' => $procurement->procurement_id,
                'activity' => $procurement->activity,
                'procurement_category' => $procurement->procurement_category, // Include procurement category
                'status' => $form ? $form->status : 'No Status', // If no form, return 'No Status'
                'unit' => $form ? $form->unit : 'No Unit', // If no form, return 'No Unit'
            ];
        });
        // Return the merged data as a JSON response
        return response()->json($mergedData);
    });

    Route::get('/ntca-by-saro', [sparkSaroController::class, 'getNtcaBySaro']);

    //spark SEARCH PROCUREMENTS
    Route::get('/search-procurement-spark', function (Request $request) {
        $query = $request->query('query');

        // Check if query exists before proceeding
        if (!$query) {
            return response()->json([], 400); // Return an empty array if no query provided
        }

        try {
            // Perform the search using the provided query parameter
            $procurements = DB::connection('spark')
                ->table('procurement')
                ->select('procurement_id', 'activity', 'procurement_category')
                ->where('procurement_id', 'like', "%{$query}%")
                ->orWhere('activity', 'like', "%{$query}%")
                ->orderBy('procurement_id', 'desc')
                ->get();

            // Fetch procurement form data (status, unit) for regular procurement
            $procurementForms = DB::connection('spark')->table('procurement_form')->get();

            // Fetch honoraria form data (status, unit) for honoraria category procurements
            $honorariaForms = DB::connection('spark')->table('honoraria_form')->get();

            // Fetch other expense form data (status, unit) for other expense category procurements
            $otherexpenseForms = DB::connection('spark')->table('otherexpense_form')->get();

            // Merge procurement data with form data
            $mergedData = $procurements->map(function ($procurement) use ($procurementForms, $honorariaForms, $otherexpenseForms) {
                // Try fetching the corresponding form for honoraria, procurement, or other expense
                $form = $honorariaForms->firstWhere('procurement_id', $procurement->procurement_id) ??
                    $procurementForms->firstWhere('procurement_id', $procurement->procurement_id) ??
                    $otherexpenseForms->firstWhere('procurement_id', $procurement->procurement_id);

                return [
                    'procurement_id' => $procurement->procurement_id,
                    'category' => $procurement->procurement_category,
                    'activity' => $procurement->activity,
                    'status' => $form && !empty($form->status) ? $form->status : '',
                    'unit' => $form && !empty($form->unit) ? $form->unit : '',
                ];
            });

            return response()->json($mergedData->values()->all());
        } catch (\Exception $e) {
            Log::error('Error searching procurement: ' . $e->getMessage());
            return response()->json(['error' => 'Error searching procurement data: ' . $e->getMessage()], 500);
        }
    });

    // spark POST REQUESTS
    Route::any('/add-saro-spark', [sparkSaroController::class, 'addSaro'])->name('add-saro-spark');
    Route::any('/add-procurement-spark', [sparkProcurementController::class, 'addProcurement'])->name('addProcurement');
    Route::get('/fetch-procurement-details', [sparkProcurementController::class, 'fetchProcurementDetails'])->name('fetchProcurementDetails');
    Route::post('/procurement/update', [sparkProcurementFormController::class, 'update']);
    Route::get('/fetch-combined-procurement', [sparkProcurementController::class, 'fetchCombinedProcurementData']);
    Route::post('/honoraria/update', [sparkHonorariaFormController::class, 'updateHonoraria']);
    Route::post('/otherexpense/update', [sparkOtherExpenseFormController::class, 'updateOtherExpense']);
    Route::get('/fetch-combined-procurement-data', [sparkProcurementController::class, 'fetchCombinedProcurementData']);
    Route::post('/requirements/upload', [sparkHonorariaFormController::class, 'upload'])->name('requirements.upload');
    Route::get('/overdue-procurements', [sparkProcurementController::class, 'getOverdueProcurements']);
    Route::get('/requirements/{procurement_id}', [sparkHonorariaFormController::class, 'getUploadedFiles']);
    Route::post('/otherexpense/upload', [sparkOtherExpenseFormController::class, 'upload'])->name('otherexpense.upload');
    Route::get('/otherexpense/requirements/{procurement_id}', [sparkOtherExpenseFormController::class, 'getUploadedFiles']);
    Route::post('/procurement/upload', [sparkProcurementFormController::class, 'upload'])->name('procurement.upload');
    Route::get('/procurement/requirements/{procurement_id}', [sparkProcurementFormController::class, 'getUploadedFiles']);
    Route::get('/uploadedHonorariaFilesCheck/{procurement_id}', [sparkHonorariaFormController::class, 'uploadedFilesCheck']);
    Route::get('/uploadedTravelExpenseFileCheck/{procurement_id}', [sparkOtherExpenseFormController::class, 'uploadedFilesCheck']);
    Route::get('/uploadedProcurementFilesCheck/{procurement_id}', [sparkProcurementFormController::class, 'uploadedFilesCheck']);
    Route::post('/save-ntca', [sparkSaroController::class, 'saveNTCA']);
    Route::get('/ntca-breakdown/{ntcaNo}', [sparkSaroController::class, 'getNTCABreakdown']);
    Route::get('/fetch-ntca-by-saro/{saroNo}', [sparkSaroController::class, 'fetchNTCABySaro']);
    Route::get('/ntca-balance/{ntcaNo}', [sparkSaroController::class, 'getNTCABalanceForCurrentQuarter']);
    Route::get('/check-overdue', [sparkProcurementController::class, 'getOverdueProcurements']);
});
