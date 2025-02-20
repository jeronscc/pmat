<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddSaroController extends Controller
{
    public function addSaro(Request $request)
    {
        // Validate the request data
        $request->validate([
            'saro_number' => 'required|string|max:255',
            'budget' => 'required|numeric',
            'year' => 'required|integer',
        ]);

        // Insert the new SARO into the database
        DB::connection('ilcdb')->table('saro')->insert([
            'saro_no' => $request->input('saro_number'),
            'current_budget' => $request->input('budget'),
            'year' => $request->input('year'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'SARO added successfully');
    }
}