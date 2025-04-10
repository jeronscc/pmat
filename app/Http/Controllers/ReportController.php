<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function getProcurementDistribution(Request $request)
{
    try {
        // Map project to database connection
        $connections = [
            'ILCDB' => 'ilcdb',
            'DTC' => 'dtc',
            'SPARK' => 'spark',
            'PROJECT CLICK' => 'click',
        ];

        // Count procurements per project
        $counts = [];

        foreach ($connections as $project => $connection) {
            // Query each project database to count the number of procurements
            $count = DB::connection($connection)
                ->table('procurement')
                ->count();

            // Store the count for the project
            $counts[$project] = $count;
        }

        return response()->json([
            'success' => true,
            'data' => $counts,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch procurement distribution.',
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

        // Log the connection being used for debugging
        \Log::info('Using connection: ' . $connection);

        // Count procurements per category
        $counts = DB::connection($connection)
            ->table('procurement')
            ->selectRaw("
                SUM(CASE WHEN procurement_category = 'SVP' THEN 1 ELSE 0 END) AS svp,
                SUM(CASE WHEN procurement_category = 'Honoraria' THEN 1 ELSE 0 END) AS honoraria,
                SUM(CASE WHEN procurement_category = 'Daily Travel Expense' THEN 1 ELSE 0 END) AS daily_travel_expense
            ")
            ->first();

        if ($counts) {
            return response()->json([
                'success' => true,
                'data' => $counts,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No data found for the selected project.',
            ], 404);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch procurement category counts.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function getExpenditureByQuarter(Request $request)
{
    try {
        // Map project to database connection
        $connections = [
            'ILCDB' => 'ilcdb',
            'DTC' => 'dtc',
            'SPARK' => 'spark',
            'PROJECT CLICK' => 'click',
            'ALL' => null,  // 'ALL' will be used to fetch total for all projects
        ];

        $quarterData = [
            'First Quarter' => 0,
            'Second Quarter' => 0,
            'Third Quarter' => 0,
            'Fourth Quarter' => 0,
        ];

        // If 'ALL' is selected, sum up for all projects
        if ($request->query('project') == 'ALL') {
            foreach ($connections as $project => $connection) {
                if ($project === 'ALL') continue;

                $expenditure = DB::connection($connection)
                    ->table('ntca')
                    ->selectRaw('SUM(first_q) as first_q, SUM(second_q) as second_q, SUM(third_q) as third_q, SUM(fourth_q) as fourth_q')
                    ->first();

                // Accumulate the values for all projects
                $quarterData['First Quarter'] += $expenditure->first_q ?? 0;
                $quarterData['Second Quarter'] += $expenditure->second_q ?? 0;
                $quarterData['Third Quarter'] += $expenditure->third_q ?? 0;
                $quarterData['Fourth Quarter'] += $expenditure->fourth_q ?? 0;
            }
        } else {
            // Fetch expenditure for a specific project
            $connection = $connections[$request->query('project')];

            $expenditure = DB::connection($connection)
                ->table('ntca')
                ->selectRaw('SUM(first_q) as first_q, SUM(second_q) as second_q, SUM(third_q) as third_q, SUM(fourth_q) as fourth_q')
                ->first();

            $quarterData['First Quarter'] = $expenditure->first_q ?? 0;
            $quarterData['Second Quarter'] = $expenditure->second_q ?? 0;
            $quarterData['Third Quarter'] = $expenditure->third_q ?? 0;
            $quarterData['Fourth Quarter'] = $expenditure->fourth_q ?? 0;
        }

        return response()->json([
            'success' => true,
            'data' => $quarterData,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch expenditure by quarter.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function getCostSavings(Request $request)
{
    try {
        // Map project to database connection
        $connections = [
            'ILCDB' => 'ilcdb',
            'DTC' => 'dtc',
            'SPARK' => 'spark',
            'PROJECT CLICK' => 'click',
        ];

        $costSavingsData = [];

        // Fetch data for a specific project
        $project = $request->query('project');
        if (isset($connections[$project])) {
            $connection = $connections[$project];

            // Fetch data for procurement_form
            $costData = DB::connection($connection)
                ->table('procurement_form')
                ->select('saro_no', 
                    DB::raw('SUM(pr_amount) as total_pr_amount'), 
                    DB::raw('SUM(budget_spent) as total_budget_spent'))
                ->groupBy('saro_no')
                ->get();

            // Repeat for other tables (otherexpense_form, honoraria_form)
            $otherCostData = DB::connection($connection)
                ->table('otherexpense_form')
                ->select('saro_no', 
                    DB::raw('SUM(pr_amount) as total_pr_amount'), 
                    DB::raw('SUM(budget_spent) as total_budget_spent'))
                ->groupBy('saro_no')
                ->get();

            $honorariaCostData = DB::connection($connection)
                ->table('honoraria_form')
                ->select('saro_no', 
                    DB::raw('SUM(pr_amount) as total_pr_amount'), 
                    DB::raw('SUM(budget_spent) as total_budget_spent'))
                ->groupBy('saro_no')
                ->get();

            // Merge the results from each table
            $allData = array_merge(
                $costData->toArray(), 
                $otherCostData->toArray(), 
                $honorariaCostData->toArray()
            );

            // Aggregate per saro_no (sum the amounts for each saro_no)
            $aggregatedData = [];
            foreach ($allData as $data) {
                if (!isset($aggregatedData[$data->saro_no])) {
                    $aggregatedData[$data->saro_no] = [
                        'saro_no' => $data->saro_no,
                        'total_pr_amount' => 0,
                        'total_budget_spent' => 0,
                    ];
                }

                $aggregatedData[$data->saro_no]['total_pr_amount'] += $data->total_pr_amount;
                $aggregatedData[$data->saro_no]['total_budget_spent'] += $data->total_budget_spent;
            }

            // Reindex the array to return a simple array of data
            $costSavingsData = array_values($aggregatedData);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid project selected.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $costSavingsData,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch cost savings data.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function getProjectReport(Request $request)
{
    $project = $request->query('project', 'ILCDB'); // Default project

    // Define your available projects and their corresponding table names and databases
    $projects = [
        'ILCDB' => [
            'database' => 'ilcdb',
            'tables' => [
                'procurement_form' => 'procurement_form',
                'honoraria_form' => 'honoraria_form',
                'otherexpense_form' => 'otherexpense_form',
                'saro' => 'saro',
                'ntca' => 'ntca'
            ]
        ],
        'SPARK' => [
            'database' => 'spark',
            'tables' => [
                'procurement_form' => 'procurement_form',
                'honoraria_form' => 'honoraria_form',
                'otherexpense_form' => 'otherexpense_form',
                'saro' => 'saro',
                'ntca' => 'ntca'
            ]
        ],
        'DTC' => [
            'database' => 'dtc',
            'tables' => [
                'procurement_form' => 'procurement_form',
                'honoraria_form' => 'honoraria_form',
                'otherexpense_form' => 'otherexpense_form',
                'saro' => 'saro',
                'ntca' => 'ntca'
            ]
        ],
        'PROJECT CLICK' => [
            'database' => 'click',
            'tables' => [
                'procurement_form' => 'procurement_form',
                'honoraria_form' => 'honoraria_form',
                'otherexpense_form' => 'otherexpense_form',
                'saro' => 'saro',
                'ntca' => 'ntca'
            ]
        ]
    ];

    // Check if the requested project exists
    if (!array_key_exists($project, $projects)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid project selected.'
        ], 400);
    }

    // Get the tables and database for the selected project
    $projectData = $projects[$project];
    $database = $projectData['database'];
    $tables = $projectData['tables'];

    try {
        // Use the specified database connection
        $connection = DB::connection($database);

        // Check if tables exist and skip missing tables
        $existingTables = [];
        foreach ($tables as $key => $table) {
            if ($connection->getSchemaBuilder()->hasTable($table)) {
                $existingTables[$key] = $table;
            }
        }

        if (empty($existingTables)) {
            throw new \Exception("No valid tables found for the selected project.");
        }

        // Average Budget Spent
        $avgBudgetSpent = $connection->table(function ($query) use ($existingTables) {
            if (isset($existingTables['procurement_form'])) {
                $query->select('budget_spent')
                    ->from($existingTables['procurement_form']);
            }
            if (isset($existingTables['honoraria_form'])) {
                $query->unionAll(
                    DB::table($existingTables['honoraria_form'])->select('budget_spent')
                );
            }
            if (isset($existingTables['otherexpense_form'])) {
                $query->unionAll(
                    DB::table($existingTables['otherexpense_form'])->select('budget_spent')
                );
            }
        }, 'combined')
        ->avg('budget_spent');

        // Total Budget Spent
        $totalBudgetSpent = $connection->table(function ($query) use ($existingTables) {
            if (isset($existingTables['procurement_form'])) {
                $query->select('budget_spent')
                    ->from($existingTables['procurement_form']);
            }
            if (isset($existingTables['honoraria_form'])) {
                $query->unionAll(
                    DB::table($existingTables['honoraria_form'])->select('budget_spent')
                );
            }
            if (isset($existingTables['otherexpense_form'])) {
                $query->unionAll(
                    DB::table($existingTables['otherexpense_form'])->select('budget_spent')
                );
            }
        }, 'combined')
        ->sum('budget_spent');

        // Average Allocated Budget (SARO)
        $avgAllocatedBudget = isset($existingTables['saro'])
            ? $connection->table($existingTables['saro'])->avg('budget_allocated')
            : 0;

        // Average Approved Budget (NTCA)
        $avgApprovedBudget = isset($existingTables['ntca'])
            ? $connection->table($existingTables['ntca'])->avg('budget_allocated')
            : 0;

        // Processing Rate
        $avgProcessingTime = $connection->table(function ($query) use ($existingTables) {
            if (isset($existingTables['procurement_form'])) {
                $query->selectRaw('
                    AVG(TIMESTAMPDIFF(HOUR, dt_submitted1, dt_received1)) +
                    AVG(TIMESTAMPDIFF(HOUR, dt_submitted2, dt_received2)) +
                    AVG(TIMESTAMPDIFF(HOUR, dt_submitted3, dt_received3)) +
                    AVG(TIMESTAMPDIFF(HOUR, dt_submitted4, dt_received4)) +
                    AVG(TIMESTAMPDIFF(HOUR, dt_submitted5, dt_received5)) +
                    AVG(TIMESTAMPDIFF(HOUR, dt_submitted6, dt_received6)) AS average_processing_time
                ')
                ->from($existingTables['procurement_form']);
            }
            if (isset($existingTables['honoraria_form'])) {
                $query->unionAll(
                    DB::table($existingTables['honoraria_form'])
                        ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, dt_submitted, dt_received)) AS average_processing_time')
                );
            }
            if (isset($existingTables['otherexpense_form'])) {
                $query->unionAll(
                    DB::table($existingTables['otherexpense_form'])
                        ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, dt_submitted, dt_received)) AS average_processing_time')
                );
            }
        }, 'combined')
        ->selectRaw('AVG(average_processing_time) AS average_processing_time')
        ->value('average_processing_time');

        // Overdue Counter
        $overdueCount = 0;

        if (isset($existingTables['procurement_form'])) {
            $overdueCount += $connection->table($existingTables['procurement_form'])->where('status', 'overdue')->count();
        }

        if (isset($existingTables['honoraria_form'])) {
            $overdueCount += $connection->table($existingTables['honoraria_form'])->where('status', 'overdue')->count();
        }

        if (isset($existingTables['otherexpense_form'])) {
            $overdueCount += $connection->table($existingTables['otherexpense_form'])->where('status', 'overdue')->count();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'totalBudgetSpent' => $totalBudgetSpent ?: 0,
                'avgBudgetSpent' => $avgBudgetSpent ?: 0,
                'avgAllocatedBudget' => $avgAllocatedBudget ?: 0,
                'avgApprovedBudget' => $avgApprovedBudget ?: 0,
                'processingRate' => $avgProcessingTime ?: 0,
                'overdueCount' => $overdueCount ?: 0,
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching project report: ' . $e->getMessage()
        ], 500);
    }
}

}
