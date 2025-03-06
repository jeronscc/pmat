<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcurementController extends Controller
{
    public function addProcurement(Request $request)
    {
        try {
            // Validate the request data with keys that match the client payload
            $request->validate([
                'category'     => 'required',
                'pr_number'    => 'required',
                'saro_number'  => 'required',
                'pr_year'      => 'required',
                'activity'     => 'required',
                'description'  => 'required',
            ]);

            // Save the procurement data to the database
            DB::connection('ilcdb')->table('procurement')->insert([
                'procurement_category' => $request->input('category'),
                'procurement_id'       => $request->input('pr_number'),
                'saro_no'              => $request->input('saro_number'),
                'year'                 => $request->input('pr_year'),
                'activity'             => $request->input('activity'),
                'description'          => $request->input('description'),
            ]);

            // Determine which table to insert into based on the category
            $category = strtolower(trim($request->input('category')));
            
            if ($category === 'svp') {
                // Insert into 'procurement_form' table
                DB::connection('ilcdb')->table('procurement_form')->insert([
                    'procurement_id' => $request->input('pr_number'),
                    'activity'       => $request->input('activity'),
                ]);
            } elseif ($category === 'honoraria') {
                // Insert into 'honoraria_form' table
                DB::connection('ilcdb')->table('honoraria_form')->insert([
                    'procurement_id' => $request->input('pr_number'),
                    'activity'       => $request->input('activity'),
                ]);
            } elseif ($category === 'other expense' || $category === 'other expenses') {
                // Insert into 'otherexpense_form' table
                DB::connection('ilcdb')->table('otherexpense_form')->insert([
                    'procurement_id' => $request->input('pr_number'),
                    'activity'       => $request->input('activity'),
                ]);
            }

            // Return a success response
            return response()->json(['message' => 'Procurement added successfully']);
        } catch (\Exception $e) {
            Log::error('Error adding procurement: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to add procurement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchProcurementData(Request $request)
    {
        $saroNo = $request->query('saro_no');

        $procurements = DB::connection('ilcdb')
            ->table('procurement')
            ->where('saro_no', $saroNo)
            ->get();

        return response()->json($procurements);
    }
    public function fetchProcurementDetails(Request $request)
    {
        $procurementId = $request->query('procurement_id');

        // Fetch procurement details from the database
        $procurement = DB::connection('ilcdb')
            ->table('procurement')
            ->where('procurement_id', $procurementId)
            ->first(); // Use first() to get a single record

        if ($procurement) {
            // Return procurement data as JSON response
            return response()->json($procurement);
        }

        // If procurement not found, return an error message
        return response()->json(['message' => 'Procurement not found.'], 404);
    }
    public function fetchCombinedProcurementData()
    {
        try {
            // Fetch procurement data from the procurement table (empty status field)
            $procurements = DB::connection('ilcdb')->table('procurement')->get();
    
            // Fetch procurement form data (with status)
            $procurementForms = DB::connection('ilcdb')->table('procurement_form')->get();
    
            // Merge the data based on procurement_id
            $mergedData = $procurements->map(function($procurement) use ($procurementForms) {
                // Debug: Check what data you are receiving from procurement and procurement_form
                \Log::info("Procurement: " . json_encode($procurement));
                $form = $procurementForms->firstWhere('procurement_id', $procurement->procurement_id);
            
                \Log::info("Matching form: " . json_encode($form)); // Debug if the form data is found
            
                return [
                    'procurement_id' => $procurement->procurement_id,
                    'activity' => $procurement->activity,
                    // If there's no matching form, status will be null, which we can check here
                    'status' => $form ? $form->status : 'No Status', // Returning 'No Status' if no match
                    'unit' => $form ? $form->unit : 'No Unit', // Returning 'No Unit' if no match
                ];
            });
            
    
            // Return the merged data as a JSON response
            return response()->json($mergedData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching procurement data: ' . $e->getMessage()], 500);
        }
    }
    
    
}


