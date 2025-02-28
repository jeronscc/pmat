<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcurementFormController extends Controller
{
    /**
     * This is for when you directly open the form via ?pr_number=PR-2025-1234 (optional, but I included your code).
     */
    public function showForm(Request $request)
    {
        // Retrieve the PR number from query parameters
        $prNumber = $request->query('pr_number');

        // Fetch procurement details from the 'procurement' table using ilcdb connection
        $procurement = DB::connection('ilcdb')
            ->table('procurement')
            ->where('procurement_id', $prNumber)
            ->first();

        // Pass activity name to the view (fallback to 'N/A' if not found)
        $activityName = $procurement ? $procurement->activity : 'N/A';

        // Pass the variables to the view
        return view('procurementform', [
            'prNumber'     => $prNumber,
            'activityName' => $activityName,
        ]);
    }

    /**
     * This is for when you click Edit and open the procurementform/{procurement_id}.
     */
    public function edit($procurement_id)
    {
        // Fetch procurement details from ilcdb
        $procurement = DB::connection('ilcdb')
            ->table('procurement')
            ->where('procurement_id', $procurement_id)
            ->first();

        // If procurement not found, you may redirect or abort (optional safety)
        if (!$procurement) {
            return redirect('/homepage-ilcdb')->with('error', 'Procurement not found.');
        }

        // Fetch procurementform details (assumes procurementform table is in default DB)
        $procurementForm = DB::table('procurementform')
            ->where('procurement_id', $procurement_id)
            ->first();

        // Create default procurementform entry if none exists
        if (!$procurementForm) {
            DB::table('procurementform')->insert([
                'procurement_id' => $procurement_id,
                'unit' => 'Supply Unit',
                'status' => 'Pending',
                'requirements_checked' => false,
            ]);

            $procurementForm = DB::table('procurementform')
                ->where('procurement_id', $procurement_id)
                ->first();
        }

        // Pass all necessary data to the view
        return view('procurementform', [
            'procurement' => $procurement,
            'procurementForm' => $procurementForm,
            'activityName' => $procurement->activity,  // Pass for direct use if needed
        ]);
    }

    /**
     * This saves the changes when the form is submitted.
     */
    public function update(Request $request, $procurement_id)
    {
        $validated = $request->validate([
            'unit' => 'required',
            'date_submitted' => 'nullable|date',
            'date_returned' => 'nullable|date',
        ]);

        // Determine status based on submitted/returned dates
        $status = 'Pending';
        if ($validated['date_submitted'] && !$validated['date_returned']) {
            $status = match ($validated['unit']) {
                'Supply Unit' => 'Supply Unit',
                'Budget Unit' => 'Budget Unit',
                'Accounting Unit' => 'Accounting Unit',
                default => 'Pending',
            };
        } elseif ($validated['date_returned']) {
            $status = 'On Hand';
        }

        // Update procurementform table
        DB::table('procurementform')
            ->where('procurement_id', $procurement_id)
            ->update([
                'unit' => $validated['unit'],
                'date_submitted' => $validated['date_submitted'],
                'date_returned' => $validated['date_returned'],
                'status' => $status,
            ]);

        // Also sync the status back to procurement table in ilcdb
        DB::connection('ilcdb')->table('procurement')
            ->where('procurement_id', $procurement_id)
            ->update(['status' => $status]);

        return redirect('/homepage-ilcdb')->with('success', 'Procurement Form Updated Successfully');
    }
}
