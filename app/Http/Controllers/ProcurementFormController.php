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

}       


