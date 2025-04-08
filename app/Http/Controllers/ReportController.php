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

    public function getProcurementCategoryCount(Request $request)
    {
        $project = $request->query('project'); // Get the project filter from the request

        try {
            // Map project to database connection
            $connections = [
                'ILCDB' => 'ilcdb',
                'DTC' => 'dtc',
                'SPARK' => 'spark',
                'PROJECT CLICK' => 'click',
            ];

            if (!isset($connections[$project])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid project selected.',
                ], 400);
            }

            $connection = $connections[$project];

            // Count procurements per category
            $counts = DB::connection($connection)
                ->table('procurement')
                ->selectRaw("
                    SUM(CASE WHEN category = 'SVP' THEN 1 ELSE 0 END) AS svp,
                    SUM(CASE WHEN category = 'Honoraria' THEN 1 ELSE 0 END) AS honoraria,
                    SUM(CASE WHEN category = 'DTE' THEN 1 ELSE 0 END) AS dte
                ")
                ->first();

            return response()->json([
                'success' => true,
                'data' => $counts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch procurement category counts.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
