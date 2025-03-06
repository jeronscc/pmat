<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OtherExpenseFormController extends Controller
{
        public function showOtherExpenseForm(Request $request)
    {
        $prNumber = $request->query('pr_number'); // Retrieve PR/Transaction Number from URL
        $activity  = $request->query('activity');  // Retrieve Activity Name from URL

        // Optionally, you might fetch additional data from the database if needed

        return view('otherexpenseform', [
            'prNumber' => $prNumber,
            'activity' => $activity,
            // You can also pass a record if you're fetching existing data, e.g., 'record' => $record
        ]);
    }
}
