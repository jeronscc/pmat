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

    public function fetchProcurementData(Request $request)
    {
        $procurements = Procurement::with('requirements')->get();
        return response()->json($procurements);
    }

    public function updateRequirement(Request $request, $id)
    {
        $requirement = Requirement::findOrFail($id);
        $requirement->update($request->all());

        // Check if all requirements are checked
        $procurement = $requirement->procurement;
        $allChecked = $procurement->requirements->every(function ($req) {
            return $req->is_checked;
        });

        if ($allChecked) {
            $procurement->update(['status' => 'Done']);
        }

        return response()->json(['message' => 'Requirement updated successfully']);
    }
}

