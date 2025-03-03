<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcurementFormController extends Controller
{
    public function showForm(Request $request)
    {
        // Retrieve the PR number from the URL query parameters
        $prNumber = $request->query('pr_number');

        // Fetch procurement details from the 'procurement' table using the PR number
        $procurement = DB::connection('ilcdb')
            ->table('procurement')
            ->where('procurement_id', $prNumber)
            ->first();

        // If a procurement record is found, use its activity; otherwise, use a default value.
        $activityName = $procurement ? $procurement->activity : 'N/A';

        // Pass the variables to the view
        return view('procurementform', [
            'prNumber'     => $prNumber,
            'activityName' => $activityName,
            // Optionally, if you want to prefill the update form from a separate table:
            'record'       => DB::connection('ilcdb')
                              ->table('procurement_form')
                              ->where('procurement_id', $prNumber)
                              ->first()           
        ]);
    }
    
           // Update the procurement_form record via AJAX
           public function update(Request $request)
           {
               // Validate the request; only the fields that are being updated are required
               $validatedData = $request->validate([
                   'procurement_id' => 'required|exists:ilcdb.procurement_form,procurement_id',
                   'dt_submitted1'  => 'nullable|date',
                   'dt_received1'   => 'nullable|date',
                   'dt_submitted2'  => 'nullable|date',
                   'dt_received2'   => 'nullable|date',
                   'dt_submitted3'  => 'nullable|date',
                   'dt_received3'   => 'nullable|date',
                   'dt_submitted4'  => 'nullable|date',
                   'dt_received4'   => 'nullable|date',
                   'dt_submitted5'  => 'nullable|date',
                   'dt_received5'   => 'nullable|date',
                   'dt_submitted6'  => 'nullable|date',
                   'dt_received6'   => 'nullable|date',
                   'budget_spent'   => 'nullable|numeric',
               ]);
       
               try {
                   $updated = DB::connection('ilcdb')->table('procurement_form')
                       ->where('procurement_id', $validatedData['procurement_id'])
                       ->update([
                           'dt_submitted1' => $validatedData['dt_submitted1'] ?? null,
                           'dt_received1'  => $validatedData['dt_received1'] ?? null,
                           'dt_submitted2' => $validatedData['dt_submitted2'] ?? null,
                           'dt_received2'  => $validatedData['dt_received2'] ?? null,
                           'dt_submitted3' => $validatedData['dt_submitted3'] ?? null,
                           'dt_received3'  => $validatedData['dt_received3'] ?? null,
                           'dt_submitted4' => $validatedData['dt_submitted4'] ?? null,
                           'dt_received4'  => $validatedData['dt_received4'] ?? null,
                           'dt_submitted5' => $validatedData['dt_submitted5'] ?? null,
                           'dt_received5'  => $validatedData['dt_received5'] ?? null,
                           'dt_submitted6' => $validatedData['dt_submitted6'] ?? null,
                           'dt_received6'  => $validatedData['dt_received6'] ?? null,
                           'budget_spent'  => $validatedData['budget_spent'] ?? null,
                       ]);
       
                   if ($updated) {
                       return response()->json(['message' => 'Procurement form updated successfully!']);
                   } else {
                       return response()->json(['message' => 'No changes made.'], 200);
                   }
               } catch (\Exception $e) {
                   Log::error('Update Error: ' . $e->getMessage());
                   return response()->json(['message' => 'An error occurred while updating the form.'], 500);
               }
           }

}       


