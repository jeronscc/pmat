<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaroController extends Controller
{
    public function addSaro(Request $request)
    {
        $request->validate([
            'saro_number' => 'required',
            'budget' => 'required|numeric|min:0',
            'saro_year' => 'required|integer',
            'saroDesc' => 'required|string',
        ]);

        try {
            DB::connection('ilcdb')->table('saro')->insert([
                'saro_no' => $request->input('saro_number'),
                'budget_allocated' => $request->input('budget'),
                'current_budget' => $request->input('budget'),
                'description' => $request->input('saroDesc'),
                'year' => $request->input('saro_year'),
            ]);

            return response()->json(['success' => true, 'message' => 'SARO added successfully.']);
        } catch (\Exception $e) {
            Log::error('Error adding SARO: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to add SARO.'], 500);
        }
    }

    public function fetchSaroData(Request $request)
    {
        $query = DB::connection('ilcdb')->table('saro')->orderBy('year', 'desc');
    
        // Check if 'year' parameter is present and not empty
        if ($request->has('year') && !empty($request->year)) {
            $query->where('year', $request->year);
        }
    
        $data = $query->get();
        return response()->json($data);
    }
    
    public function saveNTCA(Request $request)
    {
        $validatedData = $request->validate([
            'ntca_no' => 'required|string|max:64',
            'budget' => 'required|numeric|min:0',
            'quarter' => 'required|string|in:first_q,second_q,third_q,fourth_q',
            'saro_no' => 'required|string|max:64',
        ]);

        try {
            // Save NTCA details
            DB::connection('ilcdb')->table('ntca')->insert([
                'ntca_no' => $validatedData['ntca_no'],
                'budget_allocated' => $validatedData['budget'],
                'current_budget' => $validatedData['budget'],
                $validatedData['quarter'] => $validatedData['budget'], // Save the budget in the selected quarter
                'saro_no' => $validatedData['saro_no'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'NTCA saved successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save NTCA: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save NTCA. Please try again.',
            ], 500);
        }
    }
}