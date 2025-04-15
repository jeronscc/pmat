<?php

namespace App\Http\Controllers\clickController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OtherExpenseFormController extends Controller
{
    public function showForm(Request $request)
    {
        $prNumber = $request->query('pr_number');
        $activity = $request->query('activity');

        // Fetch or create record in otherexpense_form
        $record = DB::connection('click')
            ->table('otherexpense_form')
            ->where('procurement_id', $prNumber)
            ->first();

        if (!$record) {
            DB::connection('click')->table('otherexpense_form')->insert([
                'procurement_id' => $prNumber,
                'activity'       => $activity,
            ]);
            $record = DB::connection('click')
                ->table('otherexpense_form')
                ->where('procurement_id', $prNumber)
                ->first();
        }

        // Fetch procurement details
        $procurement = DB::connection('click')
            ->table('procurement')
            ->where('procurement_id', $prNumber)
            ->first();


        // If no record exists, insert a new one
        if (!$record) {
            DB::connection('click')->table('otherexpense_form')->insert([
                'procurement_id' => $prNumber,
                'activity'       => $activity,
            ]);

            // Re-fetch the record
            $record = DB::connection('click')
                ->table('otherexpense_form')
                ->where('procurement_id', $prNumber)
                ->first();
        }

        return view('clickDTE', [
            'prNumber'    => $prNumber,
            'activity'    => $activity,
            'description' => $procurement->description ?? 'No description available',
            'pr_amount' => $procurement->pr_amount,
            'record'      => $record,
            'procurement' => $procurement
        ]);
    }

    public function updateotherexpense(Request $request)
    {
        $validatedData = $request->validate([
            'procurement_id' => 'required|exists:click.otherexpense_form,procurement_id',
            'dt_submitted'   => 'nullable|date',
            'dt_received'    => 'nullable|date',
            'budget_spent'   => 'nullable|numeric',
            'ntca_no'   => 'nullable|string',
            'quarter'   => 'nullable|string',
        ]);

        try {
            Log::info("Received Data: ", $validatedData);

            // Fetch the existing record from otherexpense_form
            $record = DB::connection('click')->table('otherexpense_form')
                ->where('procurement_id', $validatedData['procurement_id'])
                ->first();

            $unit = $record->unit ?? '';
            if ($validatedData['dt_submitted']) {
                $unit = 'Budget Unit';
            }

            // Determine status based on provided dates and budget_spent
            $status = match (true) {
                ($validatedData['dt_submitted'] && !$validatedData['dt_received']) => 'Ongoing',
                ($validatedData['dt_received'] && !$validatedData['budget_spent']) => 'Pending',
                ($validatedData['budget_spent']) => 'Done',
                default => 'Done',
            };

            Log::info("Calculated Status: " . $status);

            DB::connection('click')->transaction(function () use ($validatedData, $status, $unit, $record) {
                // Update the otherexpense_form record
                DB::connection('click')->table('otherexpense_form')
                    ->where('procurement_id', $validatedData['procurement_id'])
                    ->update([
                        'dt_submitted' => $validatedData['dt_submitted']
                            ? Carbon::parse($validatedData['dt_submitted'])->format('Y-m-d H:i:s')
                            : null,
                        'dt_received'  => $validatedData['dt_received']
                            ? Carbon::parse($validatedData['dt_received'])->format('Y-m-d H:i:s')
                            : null,
                        'ntca_no' => $validatedData['ntca_no'] ?? null,
                        'quarter' => $validatedData['quarter'] ?? null,
                        'budget_spent' => $validatedData['budget_spent'] ?? null,
                        'status'       => $status,
                        'unit'         => $unit,
                    ]);

                // Update the procurement record
                DB::connection('click')->table('procurement')
                    ->where('procurement_id', $validatedData['procurement_id'])
                    ->update([
                        'ntca_no' => $validatedData['ntca_no'] ?? null,
                        'quarter' => $validatedData['quarter'] ?? null,
                ]);
            

                // Retrieve quarter and saro_no from the record
                $quarter = $record->quarter ?? null;
                $saroNo  = $record->saro_no ?? null;

                if ($saroNo && $quarter && !empty($validatedData['budget_spent'])) {
                    // Map quarter value to the corresponding column in the ntca table
                    $column = match ($quarter) {
                        'First Quarter'  => 'first_q',
                        'Second Quarter' => 'second_q',
                        'Third Quarter'  => 'third_q',
                        'Fourth Quarter' => 'fourth_q',
                        default          => null,
                    };

                    if ($column) {
                        // Deduct budget_spent from the corresponding quarter column in ntca
                        DB::connection('click')->table('ntca')
                            ->where('saro_no', $saroNo)
                            ->decrement($column, $validatedData['budget_spent']);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Daily Travel Expense form updated successfully!',
                'status'  => $status . (($status === 'Ongoing' || $status === 'Pending') ? " at $unit" : ''),
            ]);
        } catch (\Exception $e) {
            Log::error('otherexpense update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the otherexpense form.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function uploadedFilesCheck($procurement_id)
    {
        $requiredFiles = [
            'orsFile',
            'dvFile',
            'travelOrderFile',
            'appearanceFile',
            'reportFile',
            'itineraryFile',
            'certFile',
        ];

        $uploadedFiles = DB::connection('click')->table('requirements')
            ->where('procurement_id', $procurement_id)
            ->pluck('file_path', 'requirement_name')
            ->toArray();
        $missingFiles = array_diff($requiredFiles, array_keys($uploadedFiles));

        $requirementsStatus = empty($missingFiles) ? 1 : 0;

        DB::connection('click')->table('otherexpense_form')
            ->where('procurement_id', $procurement_id)
            ->update(['requirements' => $requirementsStatus]);

        return response()->json([
            'success' => true,
            'missingFiles' => $missingFiles,
            'requirementsStatus' => $requirementsStatus
        ]);
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

            $requiredFiles = [
                'orsFile',
                'dvFile',
                'travelOrderFile',
                'appearanceFile',
                'reportFile',
                'itineraryFile',
                'certFile'
            ];

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
                    DB::connection('click')->table('requirements')
                        ->where('procurement_id', $request->procurement_id)
                        ->where('requirement_name', $file)
                        ->delete();

                    // Store file path in the database
                    DB::connection('click')->table('requirements')->insert([
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
            $uploadedFiles = DB::connection('click')
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
            $files = DB::connection('click')->table('requirements')
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
}
