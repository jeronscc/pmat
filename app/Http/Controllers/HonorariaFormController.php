<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requirement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HonorariaFormController extends Controller
{
    public function showForm(Request $request)
    {
        $prNumber = $request->query('pr_number');
        $activity = $request->query('activity');

        // Fetch existing data from the honoraria_form table
        $record = DB::connection('ilcdb')
                    ->table('honoraria_form')
                    ->where('procurement_id', $prNumber)
                    ->first();

        // If no record exists, insert a new one
        if (!$record) {
            DB::connection('ilcdb')->table('honoraria_form')->insert([
                'procurement_id' => $prNumber,
                'activity'       => $activity,
            ]);

            // Re-fetch the record
            $record = DB::connection('ilcdb')
                        ->table('honoraria_form')
                        ->where('procurement_id', $prNumber)
                        ->first();
        }

        return view('honorariaform', compact('prNumber', 'activity', 'record'));
    }

    public function updateHonoraria(Request $request)
    {
        $validatedData = $request->validate([
            'procurement_id' => 'required|exists:ilcdb.honoraria_form,procurement_id',
            'dt_submitted'   => 'nullable|date',
            'dt_received'    => 'nullable|date',
            'budget_spent'   => 'nullable|numeric',
        ]);

        try {
            Log::info("Received Data: ", $validatedData);

            $unit = $validatedData['dt_submitted'] ? 'Budget Unit' : null;
            $status = ($unit === 'Budget Unit' && $validatedData['dt_submitted'] && !$validatedData['dt_received']) ? 'Pending' : 'Done';

            DB::connection('ilcdb')->transaction(function () use ($validatedData, $unit, $status) {
                DB::connection('ilcdb')->table('honoraria_form')
                    ->where('procurement_id', $validatedData['procurement_id'])
                    ->update([
                        'dt_submitted' => $validatedData['dt_submitted'] ? Carbon::parse($validatedData['dt_submitted'])->format('Y-m-d H:i:s') : null,
                        'dt_received'  => $validatedData['dt_received'] ? Carbon::parse($validatedData['dt_received'])->format('Y-m-d H:i:s') : null,
                        'budget_spent' => $validatedData['budget_spent'] ?? null,
                        'unit'         => $unit,
                        'status'       => $status,
                    ]);

                $record = DB::connection('ilcdb')->table('honoraria_form')
                            ->where('procurement_id', $validatedData['procurement_id'])
                            ->first();

                if ($record && isset($record->saro_no) && $validatedData['budget_spent']) {
                    DB::connection('ilcdb')->table('saro')
                        ->where('saro_no', $record->saro_no)
                        ->update([
                            'current_budget' => DB::raw("current_budget - " . floatval($validatedData['budget_spent']))
                        ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Honoraria form updated successfully!',
                'unit'    => $unit,
                'status'  => $status,
            ]);

        } catch (\Exception $e) {
            Log::error('Honoraria update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the honoraria form.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function upload(Request $request)
{
    try {
        // ✅ Validate procurement_id
        if (!$request->filled('procurement_id')) {
            return response()->json([
                'success' => false,
                'message' => 'The procurement ID is missing. Please select a procurement before uploading.',
            ], 400);
        }

        $validated = $request->validate([
            'procurement_id' => 'required|string',
            'orsFile' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,png',
            'dvFile' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,png',
            'contractFile' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,png',
            'classificationFile' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,png',
            'reportFile' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,png',
            'attendanceFile' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,png',
            'resumeFile' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,png',
            'govidFile' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,png',
            'payslipFile' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,png',
            'bankFile' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,png',
            'certFile' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,png',
        ]);

        $uploads = [];
        $uploadDir = public_path("uploads/requirements/{$validated['procurement_id']}");

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($validated as $field => $file) {
            if ($request->hasFile($field)) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = "uploads/requirements/{$validated['procurement_id']}/" . $fileName;
                $file->move($uploadDir, $fileName);

                Requirement::create([
                    'procurement_id'    => $validated['procurement_id'],
                    'requirement_name'  => $field,
                    'file_path'         => $filePath,
                ]);

                $uploads[] = $field;
            }
        }

        if (empty($uploads)) {
            return response()->json([
                'success' => false,
                'message' => 'No files were uploaded.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Files uploaded successfully: ' . implode(', ', $uploads),
        ]);

    } catch (\Exception $e) {
        Log::error('File upload failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage(),
        ], 500);
    }
}

}
