<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\dtcController\SaroController as DtcSaroController;
use App\Http\Controllers\dtcController\ProcurementController as DtcProcurementController;
use App\Http\Controllers\dtcController\HonorariaFormController as DtcHonorariaFormController;
use App\Http\Controllers\dtcController\ProcurementFormController as DtcProcurementFormController;
use App\Http\Controllers\dtcController\OtherExpenseFormController as DtcOtherExpenseFormController;

//FOR DTC
Route::prefix('dtc')->middleware('api')->group(function () {
    //DTC FETCH SARO
    Route::get('/fetch-saro-dtc', function (Request $request) {
        $year = $request->query('year');

        if ($year) {
            // Fetch SARO data for a specific year and order by saro_no in descending order
            $data = DB::connection('dtc')
                ->table('saro')
                ->whereYear('year', $year)
                ->select('saro_no', 'current_budget', 'year')
                ->orderBy('saro_no', 'desc')
                ->get();
        } else {
            // Fetch all SARO data and order by saro_no in descending order
            $data = DB::connection('dtc')
                ->table('saro')
                ->select('saro_no', 'current_budget', 'year')
                ->orderBy('saro_no', 'desc')
                ->get();
        }
        // Return the data as JSON
        return response()->json($data);
    });


    // DTC FETCH SARO AND PROCUREMENT DATA
    Route::get('/fetch-saro-dtc', [DtcSaroController::class, 'fetchSaroData'])->name('fetchSaroData');
    Route::get('/fetch-procurement-dtc', [DtcProcurementController::class, 'fetchProcurementData'])->name('fetchProcurementData');
    Route::get('/search-procurement-dtc', function (Request $request) {
        $query = $request->query('query');

        $procurements = DB::connection('dtc')
            ->table('procurement')
            ->select('procurement_id', 'activity', 'procurement_category')
            ->where('procurement_id', 'like', "%{$query}%")
            ->orWhere('activity', 'like', "%{$query}%")
            ->orWhere('procurement_category', 'like', "%{$query}%")
            ->orderBy('procurement_id', 'desc')
            ->get();

        return response()->json($procurements);
    });

    // DTC FETCH HONORARIACHECKLIST
    Route::get('/fetch-honorariachecklist', function () {
        // Fetch data from the honorariachecklist table in the dtc database
        $checklistItems = DB::connection('requirements')->table('honorariachecklist')->get();

        // Return data as JSON
        return response()->json($checklistItems);
    });

    // Fetch requirements for a specific SARO or all requirements if no saro_no is provided
    Route::get('/fetch-procurement-dtc', function (Request $request) {
        $saroNo = $request->query('saro_no'); // Get the saro_no from the request
        $year = $request->query('year'); // Get the year from the request

        // Fetch procurement data from the 'procurement' table
        $procurements = DB::connection('dtc')
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
        $procurementForms = DB::connection('dtc')->table('procurement_form')->get();

        // Fetch honoraria form data (status, unit) for honoraria category procurements
        $honorariaForms = DB::connection('dtc')->table('honoraria_form')->get();

        $otherexpenseForms = DB::connection('dtc')->table('otherexpense_form')->get();

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

    Route::get('/ntca-by-saro', [dtcSaroController::class, 'getNtcaBySaro']);

    //dtc SEARCH PROCUREMENTS
    Route::get('/search-procurement-dtc', function (Request $request) {
        $query = $request->query('query');

        // Check if query exists before proceeding
        if (!$query) {
            return response()->json([], 400); // Return an empty array if no query provided
        }

        try {
            // Perform the search using the provided query parameter
            $procurements = DB::connection('dtc')
                ->table('procurement')
                ->select('procurement_id', 'activity', 'procurement_category')
                ->where('procurement_id', 'like', "%{$query}%")
                ->orWhere('activity', 'like', "%{$query}%")
                ->orderBy('procurement_id', 'desc')
                ->get();

            // Fetch procurement form data (status, unit) for regular procurement
            $procurementForms = DB::connection('dtc')->table('procurement_form')->get();

            // Fetch honoraria form data (status, unit) for honoraria category procurements
            $honorariaForms = DB::connection('dtc')->table('honoraria_form')->get();

            // Fetch other expense form data (status, unit) for other expense category procurements
            $otherexpenseForms = DB::connection('dtc')->table('otherexpense_form')->get();

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

    // dtc POST REQUESTS
    Route::any('/add-saro-dtc', [DtcSaroController::class, 'addSaro'])->name('add-saro-dtc');
    Route::any('/add-procurement-dtc', [DtcProcurementController::class, 'addProcurement'])->name('addProcurement');
    Route::get('/fetch-procurement-details', [DtcProcurementController::class, 'fetchProcurementDetails'])->name('fetchProcurementDetails');
    Route::post('/procurement/update', [DtcProcurementFormController::class, 'update']);
    Route::get('/fetch-combined-procurement', [DtcProcurementController::class, 'fetchCombinedProcurementData']);
    //Route::post('/honoraria/update', [DtcHonorariaFormController::class, 'updateHonoraria']);
    Route::post('/otherexpense/update', [DtcOtherExpenseFormController::class, 'updateOtherExpense']);
    Route::get('/fetch-combined-procurement-data', [DtcProcurementController::class, 'fetchCombinedProcurementData']);
   // Route::post('/requirements/upload', [DtcHonorariaFormController::class, 'upload'])->name('requirements.upload');
    Route::get('/overdue-procurements', [DtcProcurementController::class, 'getOverdueProcurements']);
    //Route::get('/requirements/{procurement_id}', [DtcHonorariaFormController::class, 'getUploadedFiles']);
    Route::post('/otherexpense/upload', [DtcOtherExpenseFormController::class, 'upload'])->name('otherexpense.upload');
    Route::get('/otherexpense/requirements/{procurement_id}', [DtcOtherExpenseFormController::class, 'getUploadedFiles']);
    Route::post('/procurement/upload', [DtcProcurementFormController::class, 'upload'])->name('procurement.upload');
    Route::get('/procurement/requirements/{procurement_id}', [DtcProcurementFormController::class, 'getUploadedFiles']);
   // Route::get('/uploadedHonorariaFilesCheck/{procurement_id}', [DtcHonorariaFormController::class, 'uploadedFilesCheck']);
    Route::get('/uploadedTravelExpenseFileCheck/{procurement_id}', [DtcOtherExpenseFormController::class, 'uploadedFilesCheck']);
    Route::get('/uploadedProcurementFilesCheck/{procurement_id}', [DtcProcurementFormController::class, 'uploadedFilesCheck']);
    Route::post('/save-ntca', [DtcSaroController::class, 'saveNTCA']);
    Route::get('/ntca-breakdown/{ntcaNo}', [DtcSaroController::class, 'getNTCABreakdown']);
    Route::get('/fetch-ntca-by-saro/{saroNo}', [DtcSaroController::class, 'fetchNTCABySaro']);
    Route::get('/ntca-balance/{ntcaNo}', [DtcSaroController::class, 'getNTCABalanceForCurrentQuarter']);
    Route::get('/check-overdue', [DtcProcurementController::class, 'getOverdueProcurements']);
});
