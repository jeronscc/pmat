<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Saro;

class SaroController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'saro_number' => 'required|string|max:255',
            'budget' => 'required|numeric',
            'year' => 'required|integer',
        ]);

        $saro = new Saro();
        $saro->saro_number = $validatedData['saro_number'];
        $saro->budget = $validatedData['budget'];
        $saro->year = $validatedData['year'];
        $saro->save();

        return response()->json(['message' => 'SARO added successfully']);
    }
}
