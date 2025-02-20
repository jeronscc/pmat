<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HonorariaController extends Controller
{
    public function showHonorariaForm()
    {
        // Fetch data from the honorariachecklist table
        $checklistItems = DB::connection('requirements')->table('honorariachecklist')->get();

        // Pass the data to the view
        return view('honorariaform', ['checklistItems' => $checklistItems]);
    }
}