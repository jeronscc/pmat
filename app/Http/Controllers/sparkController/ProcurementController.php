<?php

namespace App\Http\Controllers\sparkController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
                'pr_amount'    => 'required',
                'approved_budget'    => 'required',
            ]);

            // Save the procurement data to the database
            DB::connection('spark')->table('procurement')->insert([
                'procurement_category' => $request->input('category'),
                'procurement_id'       => $request->input('pr_number'),
                'saro_no'              => $request->input('saro_number'),
                'year'                 => $request->input('pr_year'),
                'activity'             => $request->input('activity'),
                'description'          => $request->input('description'),
                'pr_amount'            => $request->input('pr_amount'),
                'approved_budget'            => $request->input('approved_budget'),
            ]);

            // Determine which table to insert into based on the category
            $category = strtolower(trim($request->input('category')));

            if ($category === 'svp') {
                // Insert into 'procurement_form' table
                DB::connection('spark')->table('procurement_form')->insert([
                    'procurement_id' => $request->input('pr_number'),
                    'activity'       => $request->input('activity'),
                    'saro_no'        => $request->input('saro_number'),
                    'pr_amount'      => $request->input('pr_amount'), // Add pr_amount
                    'approved_budget'      => $request->input('approved_budget'),
                ]);
            } elseif ($category === 'honoraria') {
                // Insert into 'honoraria_form' table
                DB::connection('spark')->table('honoraria_form')->insert([
                    'procurement_id' => $request->input('pr_number'),
                    'activity'       => $request->input('activity'),
                    'saro_no'        => $request->input('saro_number'),
                    'pr_amount'      => $request->input('pr_amount'), // Add pr_amount
                    'approved_budget'      => $request->input('approved_budget'),
                ]);
            } elseif ($category === 'daily travel expense' || $category === 'daily travel expenses') {
                // Insert into 'otherexpense_form' table
                DB::connection('spark')->table('otherexpense_form')->insert([
                    'procurement_id' => $request->input('pr_number'),
                    'activity'       => $request->input('activity'),
                    'saro_no'        => $request->input('saro_number'),
                    'pr_amount'      => $request->input('pr_amount'), // Add pr_amount
                    'approved_budget'      => $request->input('approved_budget'),
                ]);
            }

            // Deduct pr_amount from the current_budget of the corresponding SARO record
            DB::connection('spark')->table('saro')
                ->where('saro_no', $request->input('saro_number'))
                ->decrement('current_budget', $request->input('pr_amount'));

            // Return a success response
            return response()->json(['message' => 'Procurement added successfully']);
        } catch (\Exception $e) {
            Log::info('Request Data:', $request->all());
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

        $procurements = DB::connection('spark')
            ->table('procurement')
            ->where('saro_no', $saroNo)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($procurements);
    }

    public function fetchProcurementDetails(Request $request)
    {
        $procurementId = $request->query('procurement_id');

        // Fetch procurement details from the procurement table
        $procurement = DB::connection('spark')
            ->table('procurement')
            ->where('procurement_id', $procurementId)
            ->first();

        if ($procurement) {
            // Try to get budget_spent from procurement_form
            $budgetSpent = DB::connection('spark')
                ->table('procurement_form')
                ->where('procurement_id', $procurementId)
                ->value('budget_spent');

            // If null, try honoraria_form
            if (is_null($budgetSpent)) {
                $budgetSpent = DB::connection('spark')
                    ->table('honoraria_form')
                    ->where('procurement_id', $procurementId)
                    ->value('budget_spent');
            }

            // If still null, try otherexpense_form
            if (is_null($budgetSpent)) {
                $budgetSpent = DB::connection('spark')
                    ->table('otherexpense_form')
                    ->where('procurement_id', $procurementId)
                    ->value('budget_spent');
            }

            // Attach the first non-null budget_spent value to the procurement object
            $procurement->budget_spent = $budgetSpent;

            // Return the complete procurement data as JSON
            return response()->json($procurement);
        }

        // If procurement not found, return an error message
        return response()->json(['message' => 'Procurement not found.'], 404);
    }

    public function fetchCombinedProcurementData(Request $request)
    {
        try {
            $year = $request->query('year');
            $statusFilter = $request->query('status');

            // Fetch procurement data from the 'procurement' table (without status field)
            $procurements = DB::connection('spark')->table('procurement')
                ->when($year, function ($query, $year) {
                    return $query->whereYear('created_at', $year);
                })
                ->get();

            // Fetch procurement form data (status, unit) for regular procurement
            $procurementForms = DB::connection('spark')->table('procurement_form')->get();

            // Fetch honoraria form data (status, unit) for honoraria category procurements
            $honorariaForms = DB::connection('spark')->table('honoraria_form')->get();

            // Fetch other expense form data (status, unit) for other expense category procurements
            $otherexpenseForms = DB::connection('spark')->table('otherexpense_form')->get();

            // Merge procurement data with form data
            $mergedData = $procurements->map(function ($procurement) use ($procurementForms, $honorariaForms, $otherexpenseForms) {
                // Try fetching the corresponding form for honoraria, procurement, or other expense
                $form = $honorariaForms->firstWhere('procurement_id', $procurement->procurement_id) ??
                    $procurementForms->firstWhere('procurement_id', $procurement->procurement_id) ??
                    $otherexpenseForms->firstWhere('procurement_id', $procurement->procurement_id);

                return [
                    'procurement_id' => $procurement->procurement_id,
                    'activity' => $procurement->activity,
                    'status' => $form && !empty($form->status) ? $form->status : 'No Status', // ✅ Ensure "No Status" appears
                    'unit' => $form && !empty($form->unit) ? $form->unit : 'No Unit', // ✅ Ensure "No Unit" appears
                ];
            });

            // ✅ Only apply status filter if a specific status is selected (not "all")
            if (!empty($statusFilter) && strtolower($statusFilter) !== 'all') {
                $mergedData = $mergedData->filter(function ($item) use ($statusFilter) {
                    return strtolower($item['status']) === strtolower($statusFilter);
                });
            }

            // ✅ Ensure all procurements are returned if "all" is selected
            return response()->json($mergedData->values()->all());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching procurement data: ' . $e->getMessage()], 500);
        }
    }

    public function getOverdueProcurements()
    {
        $overdueProcurements = DB::connection('spark')->select("
            SELECT 
                procurement_id, 
                activity, 
                COALESCE(dt_submitted1, dt_submitted2, dt_submitted3, dt_submitted4, dt_submitted5, dt_submitted6) AS dt_submitted
            FROM procurement_form 
            WHERE status = 'Overdue'
            
            UNION ALL
    
            SELECT 
                procurement_id, 
                activity, 
                dt_submitted
            FROM honoraria_form 
            WHERE status = 'Overdue'
    
            UNION ALL
    
            SELECT 
                procurement_id, 
                activity, 
                dt_submitted
            FROM otherexpense_form 
            WHERE status = 'Overdue'
        ");

        return response()->json($overdueProcurements);
    }
}
