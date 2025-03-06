<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            // Use updateOrInsert to handle cases where no changes occur.
            // Alternatively, use update() and always return a JSON response.
            $updated = DB::connection('ilcdb')->table('honoraria_form')
                ->where('procurement_id', $validatedData['procurement_id'])
                ->update([
                    'dt_submitted' => $validatedData['dt_submitted']
                                        ? \Carbon\Carbon::parse($validatedData['dt_submitted'])->format('Y-m-d H:i:s')
                                        : null,
                    'dt_received'  => $validatedData['dt_received']
                                        ? \Carbon\Carbon::parse($validatedData['dt_received'])->format('Y-m-d H:i:s')
                                        : null,
                    'budget_spent' => $validatedData['budget_spent'] ?? null,
                ]);
    
            // Always return a JSON response.
            // Note: if $updated returns 0 because no data was changed, we still return a success message.
            return response()->json([
                'message' => 'Honoraria form updated successfully!',
                'updated' => $updated,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Honoraria update error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while updating the honoraria form.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}