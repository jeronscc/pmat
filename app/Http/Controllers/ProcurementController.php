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
                    'saro_no'       => $request->input('saro_number'),
                ]);
            } elseif ($category === 'honoraria') {
                // Insert into 'honoraria_form' table
                DB::connection('ilcdb')->table('honoraria_form')->insert([
                    'procurement_id' => $request->input('pr_number'),
                    'activity'       => $request->input('activity'),
                    'saro_no'       => $request->input('saro_number'),
                ]);
            } elseif ($category === 'other expense' || $category === 'other expenses') {
                // Insert into 'otherexpense_form' table
                DB::connection('ilcdb')->table('otherexpense_form')->insert([
                    'procurement_id' => $request->input('pr_number'),
                    'activity'       => $request->input('activity'),
                    'saro_no'       => $request->input('saro_number'),
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
        ->leftJoin('procurement_form', 'procurement.procurement_id', '=', 'procurement_form.procurement_id')
        ->leftJoin('honoraria_form', 'procurement.procurement_id', '=', 'honoraria_form.procurement_id')
        ->leftJoin('otherexpense_form', 'procurement.procurement_id', '=', 'otherexpense_form.procurement_id')
        ->where('procurement.saro_no', $saroNo)
        ->select(
            'procurement.*',
            DB::raw("COALESCE(procurement_form.status, honoraria_form.status, otherexpense_form.status, 'No Status') as status")
        )
        ->orderBy('procurement.created_at', 'desc')
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
        // Fetch procurement data from the 'procurement' table (without status field)
        $procurements = DB::connection('ilcdb')->table('procurement')->get();

        // Fetch procurement form data (status, unit) for regular procurement
        $procurementForms = DB::connection('ilcdb')->table('procurement_form')->get();

        // Fetch honoraria form data (status, unit) for honoraria category procurements
        $honorariaForms = DB::connection('ilcdb')->table('honoraria_form')->get();

        $otherexpenseForms = DB::connection('ilcdb')->table('otherexpense_form')->get();

        // Merge procurement data with form data
        $mergedData = $procurements->map(function ($procurement) use ($procurementForms, $honorariaForms, $otherexpenseForms) {

            // Debug: Check procurement data
            Log::info("Procurement Data: " . json_encode($procurement));
        
            // Try fetching the corresponding form for honoraria or procurement
            $form = $honorariaForms->firstWhere(function($item) use ($procurement) {
                return (string)$item->procurement_id === (string)$procurement->procurement_id;
            }) ?? $procurementForms->firstWhere(function($item) use ($procurement) {
                return (string)$item->procurement_id === (string)$procurement->procurement_id;
            }) ?? $otherexpenseForms->firstWhere(function($item) use ($procurement) {
                return (string)$item->procurement_id === (string)$procurement->procurement_id;
            });
        
            // Log form data to debug
            Log::info("Form Data: " . json_encode($form));
        
            // Return the merged data (procurement info + form info)
            return [
                'procurement_id' => $procurement->procurement_id,
                'activity' => $procurement->activity,
                'status' => $form ? $form->status : 'No Status', // If no form, return 'No Status'
                'unit' => $form ? $form->unit : 'No Unit', // If no form, return 'No Unit'
            ];
        });
        
        // Return the merged data as a JSON response
        return response()->json($mergedData);

    } catch (\Exception $e) {
        // If an error occurs, return a response with error details
        return response()->json(['error' => 'Error fetching procurement data: ' . $e->getMessage()], 500);
    }
}
public function index(Request $request)
{
    $statusFilter = $request->input('status', 'all');

    $procurements = DB::table('procurements')
        ->select('*')
        ->when($statusFilter === 'ongoing', function ($query) {
            return $query->where('status', 'Ongoing');
        })
        ->when($statusFilter === 'overdue', function ($query) {
            return $query->where('status', 'Overdue');
        })
        ->when($statusFilter === 'done', function ($query) {
            return $query->where('status', 'Done');
        })
        ->orderByRaw("
            CASE 
                WHEN status = 'Overdue' THEN 1
                WHEN status = 'Ongoing' THEN 2
                WHEN status = 'Done' THEN 3
                ELSE 4
            END
        ")
        ->get();

    return response()->json($procurements);
}

}


