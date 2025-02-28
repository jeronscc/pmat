<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcurementFormController extends Controller
{
    /**
     * Direct form access via ?pr_number=PR-XXXXXX (optional for new forms).
     */
    public function showForm(Request $request)
    {
        // Retrieve the PR number from query parameters
        $prNumber = $request->query('pr_number');

        // Fetch procurement details from 'procurement' using ilcdb connection
        $procurement = DB::connection('ilcdb')
            ->table('procurement')
            ->where('procurement_id', $prNumber)
            ->first();

        // Activity name fallback
        $activityName = $procurement ? $procurement->activity : 'N/A';

        // Pass data to view
        return view('procurementform', [
            'prNumber'     => $prNumber,
            'activityName' => $activityName,
            'procurement'  => $procurement,    // In case you want full procurement details
            'procurementForm' => null           // No form record in this case (new form)
        ]);
    }

    /**
     * Edit existing procurement form.
     */
    public function edit($procurement_id)
    {
        // Fetch procurement details
        $procurement = DB::connection('ilcdb')
            ->table('procurement')
            ->where('procurement_id', $procurement_id)
            ->first();

        // Redirect if procurement doesn't exist
        if (!$procurement) {
            return redirect('/homepage-ilcdb')->with('error', 'Procurement not found.');
        }

        // Fetch or create procurementform record
        $procurementForm = DB::table('procurementform')
            ->where('procurement_id', $procurement_id)
            ->first();

        if (!$procurementForm) {
            DB::table('procurementform')->insert([
                'procurement_id' => $procurement_id,
                'unit' => 'Supply Unit',
                'status' => 'Pending',
                'requirements_checked' => false
            ]);

            $procurementForm = DB::table('procurementform')
                ->where('procurement_id', $procurement_id)
                ->first();
        }

        // Pass all data to the view, including prNumber and activityName
        return view('procurementform', [
            'prNumber' => $procurement_id,
            'activityName' => $procurement->activity,
            'procurement' => $procurement,
            'procurementForm' => $procurementForm
        ]);
    }

    /**
     * Save form changes.
     */
    public function update(Request $request, $procurement_id)
    {
        $validated = $request->validate([
            'unit' => 'required',
            'date_submitted' => 'nullable|date',
            'date_returned' => 'nullable|date',
        ]);

        // Determine status
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

        // Sync status back to procurement in ilcdb
        DB::connection('ilcdb')->table('procurement')
            ->where('procurement_id', $procurement_id)
            ->update(['status' => $status]);

        return redirect('/homepage-ilcdb')->with('success', 'Procurement Form Updated Successfully');
    }
}
