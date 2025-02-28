<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        ]);
    }

    public function edit($procurement_id)
    {
        // Fetch procurement details
        $procurement = DB::table('procurement')->where('procurement_id', $procurement_id)->first();

        // Fetch or create procurementform details
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

        return view('procurementform', compact('procurement', 'procurementForm'));
    }

    public function update(Request $request, $procurement_id)
{
    $validated = $request->validate([
        'unit' => 'required',
        'date_submitted' => 'nullable|date',
        'date_returned' => 'nullable|date',
    ]);

    // Determine status based on submitted/returned
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

    // Update procurementform
    DB::table('procurementform')
        ->where('procurement_id', $procurement_id)
        ->update([
            'unit' => $validated['unit'],
            'date_submitted' => $validated['date_submitted'],
            'date_returned' => $validated['date_returned'],
            'status' => $status,
        ]);

    // Sync status with procurement table
    DB::table('procurement')
        ->where('procurement_id', $procurement_id)
        ->update(['status' => $status]);

    return redirect('/homepage-ilcdb')->with('success', 'Procurement Form Updated Successfully');
}

}       


