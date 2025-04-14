<?php

namespace App\Http\Controllers\ilcdbController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Requirement;

class HonorariaFormController extends Controller
{
    public function showForm(Request $request)
    {
        $prNumber = $request->query('pr_number');
        $activity = $request->query('activity');

        // Fetch or create record in honoraria_form
        $record = DB::connection('ilcdb')
            ->table('honoraria_form')
            ->where('procurement_id', $prNumber)
            ->first();

        if (!$record) {
            DB::connection('ilcdb')->table('honoraria_form')->insert([
                'procurement_id' => $prNumber,
                'activity'       => $activity,
            ]);
            $record = DB::connection('ilcdb')
                ->table('honoraria_form')
                ->where('procurement_id', $prNumber)
                ->first();
        }

        // Fetch procurement details
        $procurement = DB::connection('ilcdb')
            ->table('procurement')
            ->where('procurement_id', $prNumber)
            ->first();

        return view('honorariaform', [
            'prNumber'    => $prNumber,
            'activity'    => $activity,
            'description' => $procurement->description ?? 'No description available',
            'pr_amount' => $procurement->pr_amount,
            'record'      => $record,
            'procurement' => $procurement
        ]);
    }

    public function updateHonoraria(Request $request)
    {
        $validatedData = $request->validate([
            'procurement_id' => 'required|exists:ilcdb.honoraria_form,procurement_id',
            'dt_submitted1'  => 'nullable|date',
            'dt_received1'   => 'nullable|date',
            'dt_submitted2'  => 'nullable|date',
            'dt_received2'   => 'nullable|date',
            'ntca_no'   => 'nullable|string',
            'quarter'   => 'nullable|string',
            'budget_spent'   => 'nullable|numeric',
        ]);

        try {
            // Determine the unit based on the latest submitted date
            $unit = null;
            if ($validatedData['dt_submitted2']) {
                $unit = 'Accounting Unit';
            } elseif ($validatedData['dt_submitted1']) {
                $unit = 'Budget Unit';
            } 

            // Initialize status to null
            $status = null;

            // Handle status for Budget Unit
            if ($unit === 'Budget Unit') {
                if ($validatedData['dt_submitted1'] && !$validatedData['dt_received1']) {
                    // If dt_submitted1 is filled and dt_received1 is not, set status to ""
                    $status = 'For Obligation';
                } elseif ($validatedData['dt_received1']) {
                    // If dt_received3 is filled, set status to "Pending"
                    $status = 'Returned to User';
                }
            }

            // Handle status for Accounting Unit
            if ($unit === 'Accounting Unit') {
                if ($validatedData['dt_submitted2'] && !$validatedData['dt_received2']) {
                    // If dt_submitted2 is filled and dt_received2 is not, set status to "Pending"
                    $status = 'For payment processing';
                } elseif ($validatedData['dt_received2']) {
                    // If dt_received2 is filled, set status to "Pending"
                    $status = 'Waiting for Budget';
                }
            }

            // Handle status for when all fields are filled (Done)
            if ($validatedData['budget_spent']) {
                $status = 'Done';
            }

            // Wrap the update and budget deduction in a transaction.
            DB::connection('ilcdb')->transaction(function () use ($validatedData, $unit, $status) {
                // Update the procurement_form record.
                DB::connection('ilcdb')->table('honoraria_form')
                    ->where('procurement_id', $validatedData['procurement_id'])
                    ->update([
                        'dt_submitted1' => $validatedData['dt_submitted1'] ?? null,
                        'dt_received1'  => $validatedData['dt_received1'] ?? null,
                        'dt_submitted2' => $validatedData['dt_submitted2'] ?? null,
                        'dt_received2'  => $validatedData['dt_received2'] ?? null,
                        'ntca_no' => $validatedData['ntca_no'] ?? null,
                        'quarter' => $validatedData['quarter'] ?? null,

                        'budget_spent'  => $validatedData['budget_spent'] ?? null,
                        'unit'          => $unit,
                        'status'        => $status,
                    ]);

                // Also update the procurement table with ntca_no and quarter.
                DB::connection('ilcdb')->table('procurement')
                    ->where('procurement_id', $validatedData['procurement_id'])
                    ->update([
                        'ntca_no' => $validatedData['ntca_no'] ?? null,
                        'quarter' => $validatedData['quarter'] ?? null,
                    ]);

                // Retrieve procurement form details for quarter and saro_no
                $record = DB::connection('ilcdb')->table('honoraria_form')
                    ->where('procurement_id', $validatedData['procurement_id'])
                    ->first();

                if ($record && $record->budget_spent) {
                    $column = null;
                    switch ($record->quarter) {
                        case 'First Quarter':
                            $column = 'first_q';
                            break;
                        case 'Second Quarter':
                            $column = 'second_q';
                            break;
                        case 'Third Quarter':
                            $column = 'third_q';
                            break;
                        case 'Fourth Quarter':
                            $column = 'fourth_q';
                            break;
                    }

                    if ($column && $record->saro_no) {
                        // Deduct the budget from the respective quarter in ntca table
                        DB::connection('ilcdb')->table('ntca')
                            ->where('saro_no', $record->saro_no)
                            ->decrement($column, $record->budget_spent);
                    }
                }
            });

            return response()->json([
                'message' => 'Honoraria form updated successfully!',
                'unit'    => $unit,
                'status'  => $status
            ]);
        } catch (\Exception $e) {
            Log::error('Update Error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating the form.'], 500);
        }
    }

    public function upload(Request $request)
    {
        try {
            if (!$request->filled('procurement_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: Procurement ID is missing.',
                ], 400);
            }

            $modal = $request->input('modal'); // Get modal number from request

            // Define required files for each modal separately
            $modalFiles = [
                1 => ['orsFile', 'contractFile', 'classificationFile', 'resumeFile', 'govidFile', 'payslipFile', 'bankFile'],
                2 => ['dvFile', 'reportFile', 'attendanceFile', 'certFile'],
            ];

            $requiredFiles = $modalFiles[$modal] ?? []; // Only use files for this modal

            $uploads = [];
            $missingFiles = [];

            foreach ($requiredFiles as $file) {
                if ($request->hasFile($file)) {
                    $validated = $request->validate([
                        $file => 'file|max:5120|mimes:pdf'
                    ]);

                    $uploadDir = public_path("uploads/requirements/{$request->procurement_id}");
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $uploadedFile = $request->file($file);
                    $fileName = time() . '_' . $uploadedFile->getClientOriginalName();
                    $filePath = "uploads/requirements/{$request->procurement_id}/" . $fileName;
                    $uploadedFile->move($uploadDir, $fileName);

                    // Get the file size in bytes
                    $fileSize = filesize($uploadDir . '/' . $fileName);

                    // Delete any existing record for this file
                    DB::connection('ilcdb')->table('requirements')
                        ->where('procurement_id', $request->procurement_id)
                        ->where('requirement_name', $file)
                        ->delete();

                    // Insert the new file record with size
                    DB::connection('ilcdb')->table('requirements')->insert([
                        'procurement_id'   => $request->procurement_id,
                        'requirement_name' => $file,
                        'file_path'        => $filePath,
                        'size'             => $fileSize, // Store the file size
                    ]);

                    $uploads[] = $file;
                } else {
                    $missingFiles[] = $file;
                }
            }

            Log::info('Uploaded Files:', $uploads);
            Log::info('Missing Files:', $missingFiles);

            if (empty($uploads)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No files uploaded. Missing: ' . implode(', ', $missingFiles),
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

    public function getUploadedFiles($procurement_id)
    {
        try {
            $files = DB::connection('ilcdb')->table('requirements')
                ->where('procurement_id', $procurement_id)
                ->get();

            return response()->json([
                'success' => true,
                'files' => $files,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch uploaded files: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function uploadedFilesCheck(Request $request, $procurement_id)
    {
        $modal = $request->input('modal');

        // Define required files for each modal
        $modalFiles = [
            1 => ['orsFile', 'contractFile', 'classificationFile', 'resumeFile', 'govidFile', 'payslipFile', 'bankFile'],
            2 => ['dvFile', 'reportFile', 'attendanceFile', 'certFile'],
        ];

        // Get the required files for the specified modal
        $requiredFiles = $modalFiles[$modal] ?? [];

        // Fetch uploaded files for the specified modal from the database
        $uploadedFiles = DB::connection('ilcdb')->table('requirements')
            ->where('procurement_id', $procurement_id)
            ->pluck('file_path', 'requirement_name')
            ->toArray();

        // Calculate file status for the modal
        $fileStatus = [];
        foreach ($requiredFiles as $file) {
            $fileStatus[$file . 'Uploaded'] = array_key_exists($file, $uploadedFiles);
        }

        // Determine missing files for the specified modal
        $missingFiles = array_diff($requiredFiles, array_keys($uploadedFiles));

        // Return the response
        return response()->json([
            'success' => true,
            'fileStatus' => $fileStatus,
            'missingFiles' => $missingFiles,
        ]);
    }
}
