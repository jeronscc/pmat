<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OtherExpenseFormController extends Controller
{
    public function showForm(Request $request)
    {
        // Retrieve PR number and activity from the URL query parameters
        $prNumber = $request->query('pr_number');
        $activity  = $request->query('activity');

        // Fetch existing data from the honoraria_form table using procurement_id
        $record = DB::connection('ilcdb')
                    ->table('otherexpense_form')
                    ->where('procurement_id', $prNumber)
                    ->first();
    // If no record exists, you might want to create an empty record:
    if (!$record) {
        DB::connection('ilcdb')->table('otherexpense_form')->insert([
            'procurement_id' => $prNumber,
            'activity'       => $activity,
            // Other fields can be left null
        ]);
        // Re-fetch the record after insertion
        $record = DB::connection('ilcdb')
                    ->table('otherexpense_form')
                    ->where('procurement_id', $prNumber)
                    ->first();
    }
        return view('otherexpenseform', [
            'prNumber'   => $prNumber,
            'activity'   => $activity,
            'record'     => $record  // May be null if no record exists yet.
        ]);
    }

    public function updateOtherExpense(Request $request)
    {
        // Validate the incoming data.
        $validatedData = $request->validate([
            'procurement_id' => 'required|exists:ilcdb.otherexpense_form,procurement_id',
            'dt_submitted'   => 'nullable|date',
            'dt_received'    => 'nullable|date',
            'budget_spent'   => 'nullable|numeric',
        ]);
    
        try {
            // Log the incoming data
            Log::info("Received Data: ", $validatedData);
            
            // Initializing unit and status variables
            $unit = null;
            if ($validatedData['dt_submitted']) {
                $unit = 'Budget Unit';
            }
    
            $status = null;
            if ($unit === 'Budget Unit') {
                if ($validatedData['dt_submitted'] && !$validatedData['dt_received']) {
                    $status = 'Pending';
                } else {
                    $status = 'Done';
                }
            }
            // Log unit and status for debugging
            Log::info("Calculated Unit: " . $unit);
            Log::info("Calculated Status: " . $status);
    
            // Update the record
            $updated = DB::connection('ilcdb')->table('otherexpense_form')
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
    
            // Log the update result
            Log::info("Update result: " . $updated);
    
            // Return a success response
            return response()->json([
                'message' => 'Other expense form updated successfully!',
                'updated' => $updated,
                'unit' => $unit,
                'status' => $status,
            ], 200);
    
        } catch (\Exception $e) {
            // Log the error and return a failure response
            Log::error('Other expense update error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while updating the other expense form.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    
}
