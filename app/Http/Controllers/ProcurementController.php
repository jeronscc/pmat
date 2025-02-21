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
            // Validate the request data
            $request->validate([
                'category' => 'required|string',
                'pr_number' => 'required|string',
                'activity' => 'required|string',
                'description' => 'required|string',
            ]);

            // Save the procurement data to the database
            DB::connection('ilcdb')->table('procurement')->insert([
                'category' => $request->input('category'),
                'pr_number' => $request->input('pr_number'),
                'activity' => $request->input('activity'),
                'description' => $request->input('description'),
            ]);

            // Return a success response
            return response()->json(['message' => 'Procurement added successfully']);
        } catch (\Exception $e) {
            Log::error('Error adding procurement: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to add procurement'], 500);
        }
    }
}