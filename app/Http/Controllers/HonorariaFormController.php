<?php

namespace App\Http\Controllers;

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
            'record'      => $record,
            'procurement' => $procurement
        ]);
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
    
            // Fetch existing record
            $record = DB::connection('ilcdb')->table('honoraria_form')
                        ->where('procurement_id', $validatedData['procurement_id'])
                        ->first();
    
            $unit = $record->unit ?? '';
            if ($validatedData['dt_submitted']) {
                $unit = 'Budget Unit';
            }
    
            // Determine status
            $status = match (true) {
                (!$validatedData['dt_submitted'] && !$validatedData['dt_received']) => null,
                ($validatedData['dt_submitted'] && !$validatedData['dt_received']) => 'Ongoing',
                ($validatedData['dt_received'] && !$validatedData['budget_spent']) => 'Pending',
                ($validatedData['budget_spent']) => 'Done',
                default => 'Done',
            };
    
            Log::info("Calculated Status: " . $status);
    
            DB::connection('ilcdb')->transaction(function () use ($validatedData, $unit, $status, $record) {
                // Update honoraria_form
                DB::connection('ilcdb')->table('honoraria_form')
                    ->where('procurement_id', $validatedData['procurement_id'])
                    ->update([
                        'dt_submitted' => $validatedData['dt_submitted'] ? Carbon::parse($validatedData['dt_submitted'])->format('Y-m-d H:i:s') : null,
                        'dt_received'  => $validatedData['dt_received'] ? Carbon::parse($validatedData['dt_received'])->format('Y-m-d H:i:s') : null,
                        'budget_spent' => $validatedData['budget_spent'] ?? null,
                        'unit'         => $unit,
                        'status'       => $status,
                    ]);
    
                // Assuming that the honoraria_form record has a 'quarter' column and a 'saro_no' foreign key
                $quarter = $record->quarter ?? null;
                $saroNo  = $record->saro_no ?? null;
                
                if ($saroNo && $quarter && !empty($validatedData['budget_spent'])) {
                    $column = match ($quarter) {
                        'First Quarter'  => 'first_q',
                        'Second Quarter' => 'second_q',
                        'Third Quarter'  => 'third_q',
                        'Fourth Quarter' => 'fourth_q',
                        default          => null,
                    };
    
                    if ($column) {
                        // Deduct the budget_spent from the respective quarter column in the ntca table
                        DB::connection('ilcdb')->table('ntca')
                            ->where('saro_no', $saroNo)
                            ->decrement($column, $validatedData['budget_spent']);
                    }
                }
            });
    
            return response()->json([
                'success' => true,
                'message' => 'Honoraria form updated successfully!',
                'status'  => $status . (($status === 'Ongoing' || $status === 'Pending') ? " at $unit" : ''),
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
            if (!$request->filled('procurement_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: Procurement ID is missing.',
                ], 400);
            }

            $requiredFiles = ['orsFile', 'dvFile', 'contractFile', 'classificationFile', 'reportFile', 'attendanceFile', 'resumeFile', 'govidFile', 'payslipFile', 'bankFile', 'certFile'];

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

                    $fileName = time() . '_' . $request->file($file)->getClientOriginalName();
                    $filePath = "uploads/requirements/{$request->procurement_id}/" . $fileName;
                    $request->file($file)->move($uploadDir, $fileName);

                     // Get the file size in bytes
                    $fileSize = filesize($uploadDir . '/' . $fileName);
                    // Delete existing file entry if it exists
                    DB::connection('ilcdb')->table('requirements')
                        ->where('procurement_id', $request->procurement_id)
                        ->where('requirement_name', $file)
                        ->delete();

                    // Store file path in the database
                    DB::connection('ilcdb')->table('requirements')->insert([
                        'procurement_id'    => $request->procurement_id,
                        'requirement_name'  => $file,
                        'file_path'         => $filePath,
                        'size'             => $fileSize,
                    ]);

                    $uploads[] = $file;
                } else {
                    $missingFiles[] = $file;
                }
            }

            if (empty($uploads)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No files uploaded. Missing: ' . implode(', ', $missingFiles),
                ], 400);
            }

            // Fetch the latest uploaded files after saving
            $uploadedFiles = DB::connection('ilcdb')
                ->table('requirements')
                ->where('procurement_id', $request->procurement_id)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully: ' . implode(', ', $uploads),
                'files'   => $uploadedFiles, // ✅ Return updated files
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

    public function uploadedFilesCheck($procurement_id)
    {
        $requiredFiles = [
            'orsFile', 
            'dvFile', 
            'contractFile', 
            'classificationFile', 
            'reportFile', 
            'attendanceFile', 
            'resumeFile', 
            'govidFile', 
            'payslipFile', 
            'bankFile', 
            'certFile'
        ];

        $uploadedFiles = DB::connection('ilcdb')->table('requirements')
            ->where('procurement_id', $procurement_id)
            ->pluck('file_path','requirement_name')
            ->toArray();
        $missingFiles = array_diff($requiredFiles, array_keys($uploadedFiles));

        $requirementsStatus = empty($missingFiles)? 1 : 0;

        DB::connection('ilcdb')->table('honoraria_form')
            ->where('procurement_id', $procurement_id)
            ->update(['requirements' => $requirementsStatus]);

        return response()->json([
            'success'=> true,
            'missingFiles'=> $missingFiles,
            'requirementsStatus'=> $requirementsStatus
        ]);



    }

    
}