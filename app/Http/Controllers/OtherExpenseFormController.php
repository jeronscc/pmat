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
    $prNumber = $request->query('pr_number');
    $activity = $request->query('activity');

    // Fetch existing data from the honoraria_form table
    $record = DB::connection('ilcdb')
                ->table('honoraria_form')
                ->where('procurement_id', $prNumber)
                ->first();

    // If no record exists, insert a new one
    if (!$record) {
        DB::connection('ilcdb')->table('honoraria_form')->insert([
            'procurement_id' => $prNumber,
            'activity'       => $activity,
        ]);

        // Re-fetch the record
        $record = DB::connection('ilcdb')
                    ->table('honoraria_form')
                    ->where('procurement_id', $prNumber)
                    ->first();
    }

    // Fetch the description from the procurement table
    $procurement = DB::connection('ilcdb')
        ->table('procurement')
        ->where('procurement_id', $prNumber)
        ->first();

    return view('honorariaform', [
        'prNumber'    => $prNumber,
        'activity'    => $activity,
        'description' => $procurement->description ?? 'No description available', // Get description from procurement
        'record'      => $record,
        'procurement' => $procurement
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
            
            // Initialize unit and status variables
            $unit = $validatedData['dt_submitted'] ? 'Budget Unit' : null;
            $status = null;
            if ($unit === 'Budget Unit') {
                if ($validatedData['dt_submitted'] && !$validatedData['dt_received']) {
                    $status = 'Pending';
                } else {
                    $status = 'Done';
                }
            }
            Log::info("Calculated Unit: " . $unit);
            Log::info("Calculated Status: " . $status);
    
            // Wrap the update in a transaction to ensure both operations succeed together.
            DB::connection('ilcdb')->transaction(function () use ($validatedData, $unit, $status) {
                // Update the otherexpense_form record.
                DB::connection('ilcdb')->table('otherexpense_form')
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
    
                // Retrieve the updated record to get the saro_no.
                $record = DB::connection('ilcdb')->table('otherexpense_form')
                            ->where('procurement_id', $validatedData['procurement_id'])
                            ->first();
    
                // If a matching saro_no is found and budget_spent is provided, perform the deduction.
                if ($record && isset($record->saro_no) && $validatedData['budget_spent']) {
                    DB::connection('ilcdb')->table('saro')
                        ->where('saro_no', $record->saro_no)
                        ->update([
                            'current_budget' => DB::raw("current_budget - " . floatval($validatedData['budget_spent']))
                        ]);
                }
            });
    
            // Return a success response.
            return response()->json([
                'message' => 'Other expense form updated successfully!',
                'unit'    => $unit,
                'status'  => $status,
            ], 200);
    
        } catch (\Exception $e) {
            // Log the error and return a failure response.
            Log::error('Other expense update error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while updating the other expense form.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    
}
