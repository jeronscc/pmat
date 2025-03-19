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
            // Check if the NTCA already exists
            $existingNTCA = DB::connection('ilcdb')->table('ntca')->where('ntca_no', $validatedData['ntca_no'])->first();

            if ($existingNTCA) {
                // Update the existing NTCA for the specified quarter
                DB::connection('ilcdb')->table('ntca')
                    ->where('ntca_no', $validatedData['ntca_no'])
                    ->update([
                        $validatedData['quarter'] => DB::raw("{$validatedData['quarter']} + {$validatedData['budget']}"),
                        'current_budget' => DB::raw("budget_allocated - (COALESCE(first_q, 0) + COALESCE(second_q, 0) + COALESCE(third_q, 0) + COALESCE(fourth_q, 0))"),
                    ]);
            } else {
                // Fetch the SARO's budget
                $saro = DB::connection('ilcdb')->table('saro')->where('saro_no', $validatedData['saro_no'])->first();

                if (!$saro) {
                    return response()->json([
                        'success' => false,
                        'message' => 'SARO not found.',
                    ], 404);
                }

                // Insert a new NTCA record
                DB::connection('ilcdb')->table('ntca')->insert([
                    'ntca_no' => $validatedData['ntca_no'],
                    'budget_allocated' => $saro->budget_allocated, // Use SARO's budget
                    'current_budget' => $saro->budget_allocated - $validatedData['budget'],
                    $validatedData['quarter'] => $validatedData['budget'], // Save the budget in the selected quarter
                    'saro_no' => $validatedData['saro_no'],
                ]);
            }

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

    public function getNTCABreakdown($ntcaNo)
    {
        try {
            $ntca = DB::connection('ilcdb')->table('ntca')->where('ntca_no', $ntcaNo)->first();

            if (!$ntca) {
                return response()->json([
                    'success' => false,
                    'message' => 'NTCA not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'ntca' => $ntca,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch NTCA breakdown: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch NTCA breakdown.',
            ], 500);
        }
    }

    public function fetchNTCABySaro($saroNo)
    {
        try {
            $ntcaRecords = DB::connection('ilcdb')->table('ntca')->where('saro_no', $saroNo)->get();

            if ($ntcaRecords->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No NTCA records found for the selected SARO.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'ntca' => $ntcaRecords,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch NTCA by SARO: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch NTCA records. Please try again.',
            ], 500);
        }
    }

    public function getNTCABalanceForCurrentQuarter($ntcaNo)
    {
        try {
            $ntca = DB::connection('ilcdb')->table('ntca')->where('ntca_no', $ntcaNo)->first();

            if (!$ntca) {
                return response()->json([
                    'success' => false,
                    'message' => 'NTCA not found.',
                ], 404);
            }

            // Determine the current quarter
            $currentMonth = date('n'); // Get the current month (1-12)
            $currentQuarter = match (true) {
                $currentMonth <= 3 => 'first_q',
                $currentMonth <= 6 => 'second_q',
                $currentMonth <= 9 => 'third_q',
                default => 'fourth_q',
            };

            return response()->json([
                'success' => true,
                'currentQuarter' => ucfirst(str_replace('_q', ' Quarter', $currentQuarter)),
                'balance' => $ntca->$currentQuarter ?? 0,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch NTCA balance for current quarter: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch NTCA balance. Please try again.',
            ], 500);
        }
    }
}