<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getAverageBudgetSpent()
    {
        try {
            $projects = ['click', 'ilcdb', 'spark', 'dtc'];
            $averages = [];

            foreach ($projects as $project) {
                $average = DB::connection($project)
                    ->table('ntca')
                    ->avg('budget_spent');
                $averages[$project] = round($average, 2);
            }

            return response()->json([
                'success' => true,
                'data' => $averages,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch average budget spent.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
