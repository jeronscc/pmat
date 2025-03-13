<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OtherExpenseFormController extends Controller
{
    public function showForm(Request $request)
    {
        // Retrieve PR number and activity from the URL query parameters
        $prNumber = $request->query('pr_number');
        $activity  = $request->query('activity');

        // Fetch existing data from the otherexpense_form table using procurement_id
        $record = DB::connection('ilcdb')
                    ->table('otherexpense_form')
                    ->where('procurement_id', $prNumber)
                    ->first();
    // If no record exists, you might want to create an empty record:
    if (!$record) {
        DB::connection('ilcdb')->table('otherexpense_form')->insert([
            'procurement_id' => $prNumber,
            'activity'       => $activity,
            // Other fields can be left null
        ]);
        // Re-fetch the record after insertion
        $record = DB::connection('ilcdb')
                    ->table('otherexpense_form')
                    ->where('procurement_id', $prNumber)
                    ->first();
    }
        return view('otherexpenseform', [
            'prNumber'   => $prNumber,
            'activity'   => $activity,
            'record'     => $record  // May be null if no record exists yet.
        ]);
    }

    public function updateOtherExpense(Request $request)
    {
        // Validate the incoming data.
        $validatedData = $request->validate([
            'procurement_id' => 'required|exists:ilcdb.otherexpense_form,procurement_id',
            'dt_submitted'   => 'nullable|date',
            'dt_received'    => 'nullable|date',
            'budget_spent'   => 'nullable|numeric',
        ]);
    
        try {
            // Log the incoming data
            Log::info("Received Data: ", $validatedData);
            
            // Initialize status variable
            $status = 'null'; // Default status

            if ($validatedData['dt_submitted'] && !$validatedData['dt_received']) {
                $status = 'Ongoing';
            } elseif ($validatedData['dt_received'] && !$validatedData['budget_spent']) {
                $status = 'Pending';
            } elseif ($validatedData['budget_spent']) {
                $status = 'Done';
            }

            Log::info("Calculated Status: " . $status);
    
            // Wrap the update in a transaction to ensure both operations succeed together.
            DB::connection('ilcdb')->transaction(function () use ($validatedData, $status) {
                // Update the otherexpense_form record.
                DB::connection('ilcdb')->table('otherexpense_form')
                    ->where('procurement_id', $validatedData['procurement_id'])
                    ->update([
                        'dt_submitted' => $validatedData['dt_submitted']
                            ? \Carbon\Carbon::parse($validatedData['dt_submitted'])->format('Y-m-d H:i:s')
                            : null,
                        'dt_received'  => $validatedData['dt_received']
                            ? \Carbon\Carbon::parse($validatedData['dt_received'])->format('Y-m-d H:i:s')
                            : null,
                        'budget_spent' => $validatedData['budget_spent'] ?? null,
                        'status'       => $status,
                    ]);
    
                // Retrieve the updated record to get the saro_no.
                $record = DB::connection('ilcdb')->table('otherexpense_form')
                            ->where('procurement_id', $validatedData['procurement_id'])
                            ->first();
    
                // If a matching saro_no is found and budget_spent is provided, perform the deduction.
                if ($record && isset($record->saro_no) && $validatedData['budget_spent']) {
                    DB::connection('ilcdb')->table('saro')
                        ->where('saro_no', $record->saro_no)
                        ->update([
                            'current_budget' => DB::raw("current_budget - " . floatval($validatedData['budget_spent']))
                        ]);
                }
            });
    
            // Return a success response.
            return response()->json([
                'message' => 'Other expense form updated successfully!',
                'status'  => $status,
            ], 200);
    
        } catch (\Exception $e) {
            // Log the error and return a failure response.
            Log::error('Other expense update error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while updating the other expense form.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function upload(Request $request)
    {
        try {
            // Validate procurement_id first
            if (!$request->filled('procurement_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: Procurement ID is missing.',
                ], 400);
            }

            // Define required files
            $requiredFiles = [
                'orsFile', 'dvFile', 'contractFile', 'classificationFile', 'reportFile',
                'attendanceFile', 'resumeFile', 'govidFile', 'payslipFile', 'bankFile', 'certFile'
            ];

            $uploads = [];
            $missingFiles = [];

            // Check if files are uploaded
            foreach ($requiredFiles as $file) {
                if ($request->hasFile($file)) {
                    $validated = $request->validate([
                        $file => 'file|max:5120|mimes:pdf,doc,docx,jpg,png'
                    ]);

                    $uploadDir = public_path("uploads/requirements/{$request->procurement_id}");
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileName = time() . '_' . $request->file($file)->getClientOriginalName();
                    $filePath = "uploads/requirements/{$request->procurement_id}/" . $fileName;
                    $request->file($file)->move($uploadDir, $fileName);

                    // Delete existing file entry if it exists
                    DB::connection('ilcdb')->table('requirements')
                        ->where('procurement_id', $request->procurement_id)
                        ->where('requirement_name', $file)
                        ->delete();

                    // Store file path in the ilcdb database requirements table
                    DB::connection('ilcdb')->table('requirements')->insert([
                        'procurement_id'    => $request->procurement_id,
                        'requirement_name'  => $file,
                        'file_path'         => $filePath,
                    ]);

                    $uploads[] = $file;
                } else {
                    $missingFiles[] = $file;
                }
            }

            // If no files were uploaded, return an error
            if (empty($uploads)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No files uploaded. Missing: ' . implode(', ', $missingFiles),
                ], 400);
            }

            // Update the other expense form with the uploaded files
            $this->updateOtherExpense($request);

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
}