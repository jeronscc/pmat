<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requirement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HonorariaFormController extends Controller
{
    public function showForm(Request $request)
    {
        // Retrieve PR number and activity from the URL query parameters
        $prNumber = $request->query('pr_number');
        $activity  = $request->query('activity');

        // Fetch existing data from the honoraria_form table using procurement_id
        $record = DB::connection('ilcdb')
                    ->table('honoraria_form')
                    ->where('procurement_id', $prNumber)
                    ->first();
    // If no record exists, you might want to create an empty record:
    if (!$record) {
        DB::connection('ilcdb')->table('honoraria_form')->insert([
            'procurement_id' => $prNumber,
            'activity'       => $activity,
            // Other fields can be left null
        ]);
        // Re-fetch the record after insertion
        $record = DB::connection('ilcdb')
                    ->table('honoraria_form')
                    ->where('procurement_id', $prNumber)
                    ->first();
    }
        return view('honorariaform', [
            'prNumber'   => $prNumber,
            'activity'   => $activity,
            'record'     => $record  // May be null if no record exists yet.
        ]);
    }

public function updateHonoraria(Request $request)
{
    // Validate the incoming data.
    $validatedData = $request->validate([
        'procurement_id' => 'required|exists:ilcdb.honoraria_form,procurement_id',
        'dt_submitted'   => 'nullable|date',
        'dt_received'    => 'nullable|date',
        'budget_spent'   => 'nullable|numeric',
    ]);

    try {
        // Log the incoming data
        Log::info("Received Data: ", $validatedData);

        // Determine unit and status based on submitted dates
        $unit = $validatedData['dt_submitted'] ? 'Budget Unit' : null;
        $status = ($unit === 'Budget Unit' && $validatedData['dt_submitted'] && !$validatedData['dt_received']) ? 'Pending' : 'Done';

        // Log unit and status for debugging
        Log::info("Calculated Unit: " . $unit);
        Log::info("Calculated Status: " . $status);

        // Wrap the update in a transaction to ensure data consistency
        DB::connection('ilcdb')->transaction(function () use ($validatedData, $unit, $status) {
            // Update honoraria_form record
            DB::connection('ilcdb')->table('honoraria_form')
                ->where('procurement_id', $validatedData['procurement_id'])
                ->update([
                    'dt_submitted' => $validatedData['dt_submitted']
                        ? \Carbon\Carbon::parse($validatedData['dt_submitted'])->format('Y-m-d H:i:s')
                        : null,
                    'dt_received'  => $validatedData['dt_received']
                        ? \Carbon\Carbon::parse($validatedData['dt_received'])->format('Y-m-d H:i:s')
                        : null,
                    'budget_spent' => $validatedData['budget_spent'] ?? null,
                    'unit'         => $unit,
                    'status'       => $status,
                ]);

            // Fetch the updated honoraria_form record to get the saro_no
            $record = DB::connection('ilcdb')->table('honoraria_form')
                        ->where('procurement_id', $validatedData['procurement_id'])
                        ->first();

            // Check if the record contains a saro_no and that a budget_spent value was provided
            if ($record && isset($record->saro_no) && $validatedData['budget_spent']) {
                // Deduct the budget_spent from the current_budget of the matching saro record.
                DB::connection('ilcdb')->table('saro')
                    ->where('saro_no', $record->saro_no)
                    ->update([
                        'current_budget' => DB::raw("current_budget - " . floatval($validatedData['budget_spent']))
                    ]);
            }
        });

        // Return a success response
        return response()->json([
            'message' => 'Honoraria form updated successfully!',
            'unit'    => $unit,
            'status'  => $status,
        ], 200);

    } catch (\Exception $e) {
        // Log the error and return a failure response
        Log::error('Honoraria update error: ' . $e->getMessage());
        return response()->json([
            'message' => 'An error occurred while updating the honoraria form.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function upload(Request $request)
{
    $validated = $request->validate([
        'procurement_id' => 'required|string',
        'orsFile' => 'nullable|file|max:5120',
        'dvFile' => 'nullable|file|max:5120',
        'contractFile' => 'nullable|file|max:5120',
        'classificationFile' => 'nullable|file|max:5120',
        'reportFile' => 'nullable|file|max:5120',
        'attendanceFile' => 'nullable|file|max:5120',
        'resumeFile' => 'nullable|file|max:5120',
        'govidFile' => 'nullable|file|max:5120',
        'payslipFile' => 'nullable|file|max:5120',
        'bankFile' => 'nullable|file|max:5120',
        'certFile' => 'nullable|file|max:5120',
    ]);

    $uploads = [];

    foreach ($validated as $field => $file) {
        if ($request->hasFile($field)) {
            $path = $request->file($field)->store("requirements/{$validated['procurement_id']}", 'local');

            Requirement::create([
                'procurement_id' => $validated['procurement_id'],
                'requirement_name' => $field,
                'file_path' => $path,
            ]);

            $uploads[] = $field;
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Uploaded: ' . implode(', ', $uploads)
    ]);
}
}