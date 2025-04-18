<?php

namespace App\Http\Controllers\sparkController;

use App\Http\Controllers\Controller;
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
            'date_released' => 'required|date',
        ]);

        try {
            DB::connection('spark')->table('saro')->insert([
                'saro_no' => $request->input('saro_number'),
                'budget_allocated' => $request->input('budget'),
                'current_budget' => $request->input('budget'),
                'description' => $request->input('saroDesc'),
                'year' => $request->input('saro_year'),
                'date_released' => $request->input('date_released'),
            ]);

            return response()->json(['success' => true, 'message' => 'SARO added successfully.']);
        } catch (\Exception $e) {
            Log::error('Error adding SARO: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to add SARO.'], 500);
        }
    }

    public function fetchSaroData(Request $request)
    {
        $query = DB::connection('spark')->table('saro')->orderBy('year', 'desc');

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
            'date_released' => 'required|date',
        ]);

        try {
            // Check if the NTCA already exists
            $existingNTCA = DB::connection('spark')->table('ntca')->where('ntca_no', $validatedData['ntca_no'])->first();

            if ($existingNTCA) {
                // Update the existing NTCA for the specified quarter
                DB::connection('spark')->table('ntca')
                    ->where('ntca_no', $validatedData['ntca_no'])
                    ->update([
                        $validatedData['quarter'] => DB::raw("COALESCE({$validatedData['quarter']}, 0) + {$validatedData['budget']}"),
                        'current_budget' => DB::raw("budget_allocated - (COALESCE(first_q, 0) + COALESCE(second_q, 0) + COALESCE(third_q, 0) + COALESCE(fourth_q, 0))"),
                        'date_released' => $validatedData['date_released'],
                    ]);
            } else {
                // Fetch the SARO's budget
                $saro = DB::connection('spark')->table('saro')->where('saro_no', $validatedData['saro_no'])->first();

                if (!$saro) {
                    return response()->json([
                        'success' => false,
                        'message' => 'SARO not found.',
                    ], 404);
                }

                // Insert a new NTCA record
                DB::connection('spark')->table('ntca')->insert([
                    'ntca_no' => $validatedData['ntca_no'],
                    'budget_allocated' => $saro->budget_allocated, // Use SARO's budget
                    'current_budget' => $saro->budget_allocated - $validatedData['budget'],
                    $validatedData['quarter'] => $validatedData['budget'], // Save the budget in the selected quarter
                    'saro_no' => $validatedData['saro_no'],
                    'date_released' => $validatedData['date_released'],
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
            $ntca = DB::connection('spark')->table('ntca')->where('ntca_no', $ntcaNo)->first();

            if (!$ntca) {
                return response()->json([
                    'success' => false,
                    'message' => 'NTCA not found.',
                ], 404);
            }

            $firstQuarter = $ntca->first_q ?? 0;
            $secondQuarter = $ntca->second_q ?? 0;
            $thirdQuarter = $ntca->third_q ?? 0;
            $fourthQuarter = $ntca->fourth_q ?? 0;
            $totalQuarters = $firstQuarter + $secondQuarter + $thirdQuarter + $fourthQuarter;

            return response()->json([
                'success' => true,
                'ntca' => [
                    'ntca_no' => $ntca->ntca_no,
                    'first_q' => $firstQuarter,
                    'second_q' => $secondQuarter,
                    'third_q' => $thirdQuarter,
                    'fourth_q' => $fourthQuarter,
                    'current_budget' => $ntca->current_budget ?? 0,
                    'total_quarters' => $totalQuarters,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch NTCA breakdown: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch NTCA breakdown. Please try again.',
            ], 500);
        }
    }

    public function fetchNTCABySaro($saroNo)
    {
        try {
            $ntcaRecords = DB::connection('spark')->table('ntca')->where('saro_no', $saroNo)->get();

            if ($ntcaRecords->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No NTCA records found for the selected SARO.',
                ], 404);
            }

            foreach ($ntcaRecords as $ntca) {
                $ntca->ntca_budget = DB::connection('spark')->table('ntca')->where('ntca_no', $ntca->ntca_no)->value('budget_allocated'); // From ntca table
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
            $ntca = DB::connection('spark')->table('ntca')->where('ntca_no', $ntcaNo)->first();

            if (!$ntca) {
                return response()->json([
                    'success' => false,
                    'message' => 'NTCA not found.',
                ], 404);
            }

            // Determine the most recent quarter with a value
            $quarters = [
                '4th' => $ntca->fourth_q ?? 0,
                '3rd' => $ntca->third_q ?? 0,
                '2nd' => $ntca->second_q ?? 0,
                '1st' => $ntca->first_q ?? 0,
            ];

            $currentQuarter = 'No Quarter';
            $balance = 0;

            foreach ($quarters as $quarter => $value) {
                if ($value > 0) {
                    $currentQuarter = $quarter . ' Quarter';
                    $balance = $value;
                    break;
                }
            }

            return response()->json([
                'success' => true,
                'currentQuarter' => $currentQuarter,
                'balance' => $balance,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch NTCA balance for current quarter: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch NTCA balance. Please try again.',
            ], 500);
        }
    }
    public function getNtcaBySaro(Request $request)
    {
        $saroNo = $request->query('saro_no');
    
        $ntcaNumbers = DB::connection('spark')
            ->table('ntca')
            ->select('ntca_no')
            ->where('saro_no', $saroNo)
            ->get();
    
        return response()->json($ntcaNumbers);
    }
}
