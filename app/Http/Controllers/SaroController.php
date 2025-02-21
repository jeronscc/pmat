<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaroController extends Controller
{
    public function addSaro(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'saro_number' => 'required|string',
                'budget' => 'required|numeric',
                'year' => 'required|integer',
            ]);

            // Save the SARO data to the database
            DB::connection('ilcdb')->table('saro')->insert([
                'saro_no' => $request->input('saro_number'),
                'current_budget' => $request->input('budget'),
                'year' => $request->input('year'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Return a success response
            return response()->json(['message' => 'SARO added successfully']);
        } catch (\Exception $e) {
            Log::error('Error adding SARO: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to add SARO'], 500);
        }
    }
}