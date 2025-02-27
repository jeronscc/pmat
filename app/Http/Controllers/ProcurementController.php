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

             // Insert into 'procurement_form' table
            DB::connection('ilcdb')->table('procurement_form')->insert([
                'procurement_id' => $request->input('pr_number'),
                'activity'       => $request->input('activity'),
            ]);

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
}


