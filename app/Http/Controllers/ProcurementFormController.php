<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcurementFormController extends Controller
{
    public function showForm(Request $request)
{
    $prNumber = $request->query('pr_number');

    // Fetch procurement details (including description)
    $procurement = DB::connection('ilcdb')
        ->table('procurement')
        ->where('procurement_id', $prNumber)
        ->first();

    // Fetch procurement_form details (without description since it's in procurement)
    $record = DB::connection('ilcdb')
        ->table('procurement_form')
        ->where('procurement_id', $prNumber)
        ->first();

    return view('procurementform', [
        'prNumber'     => $prNumber,
        'activityName' => $procurement->activity ?? 'N/A',
        'description'  => $procurement->description ?? 'No description available', // Get description from procurement
        'record'       => $record, 
        'procurement'  => $procurement
    ]);
}

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'procurement_id' => 'required|exists:ilcdb.procurement_form,procurement_id',
            'dt_submitted1'  => 'nullable|date',
            'dt_received1'   => 'nullable|date',
            'dt_submitted2'  => 'nullable|date',
            'dt_received2'   => 'nullable|date',
            'dt_submitted3'  => 'nullable|date',
            'dt_received3'   => 'nullable|date',
            'dt_submitted4'  => 'nullable|date',
            'dt_received4'   => 'nullable|date',
            'dt_submitted5'  => 'nullable|date',
            'dt_received5'   => 'nullable|date',
            'dt_submitted6'  => 'nullable|date',
            'dt_received6'   => 'nullable|date',
            'budget_spent'   => 'nullable|numeric',
        ]);
    
        try {
            // Determine the unit based on the latest submitted date
            $unit = null;
            if ($validatedData['dt_submitted6']) {
                $unit = 'Accounting Unit';
            } elseif ($validatedData['dt_submitted5'] || $validatedData['dt_submitted4']) {
                $unit = 'Supply Unit'; // Fix: Supply Unit for post-procurement as well
            } elseif ($validatedData['dt_submitted3']) {
                $unit = 'Budget Unit';
            } elseif ($validatedData['dt_submitted2'] || $validatedData['dt_submitted1']) {
                $unit = 'Supply Unit'; // Supply Unit for pre-procurement
            }
    
            // Initialize status to null
            $status = null;
    
            // Handle status for Supply Unit (Pre-Procurement and Post-Procurement)
            if ($unit === 'Supply Unit') {
                if ($validatedData['dt_submitted5'] && !$validatedData['dt_received5']) {
                    // If dt_submitted5 is filled and dt_received5 is not, set status to "Pending"
                    $status = 'Ongoing';
                } elseif ($validatedData['dt_received5']) {
                    // If dt_received5 is filled, set status to "Pending"
                    $status = 'Pending';
                } elseif ($validatedData['dt_submitted4'] && !$validatedData['dt_received4']) {
                    // If dt_submitted4 is filled and dt_received4 is not, set status to ""
                    $status = 'Ongoing';
                } elseif ($validatedData['dt_received4']) {
                    // If dt_received4 is filled, set status to "Pending"
                    $status = 'Pending';
                } elseif ($validatedData['dt_submitted2'] && !$validatedData['dt_received2']) {
                    // If dt_submitted2 is filled and dt_received2 is not, set status to ""
                    $status = 'Ongoing';
                } elseif ($validatedData['dt_received2']) {
                    // If dt_received2 is filled, set status to "Pending"
                    $status = 'Pending';
                } elseif ($validatedData['dt_submitted1'] && !$validatedData['dt_received1']) {
                    // If dt_submitted1 is filled and dt_received1 is not, set status to ""
                    $status = 'Ongoing';
                } elseif ($validatedData['dt_received1']) {
                    // If dt_received1 is filled, set status to "Pending"
                    $status = 'Pending';
                }
            }
    
            // Handle status for Budget Unit
            if ($unit === 'Budget Unit') {
                if ($validatedData['dt_submitted3'] && !$validatedData['dt_received3']) {
                    // If dt_submitted3 is filled and dt_received3 is not, set status to ""
                    $status = 'Ongoing';
                } elseif ($validatedData['dt_received3']) {
                    // If dt_received3 is filled, set status to "Pending"
                    $status = 'Pending';
                }
            }
    
            // Handle status for Accounting Unit
            if ($unit === 'Accounting Unit') {
                if ($validatedData['dt_submitted6'] && !$validatedData['dt_received6']) {
                    // If dt_submitted6 is filled and dt_received6 is not, set status to "Pending"
                    $status = 'Ongoing';
                } elseif ($validatedData['dt_received6']) {
                    // If dt_received6 is filled, set status to "Pending"
                    $status = 'Pending';
                }
            }
    
            // Handle status for when all fields are filled (Done)
            $allCompleted = (
                $validatedData['budget_spent']
            );
    
            if ($allCompleted) {
                $status = 'Done'; // All fields are completed
            }
    
            // Wrap the update and budget deduction in a transaction.
            DB::connection('ilcdb')->transaction(function () use ($validatedData, $unit, $status) {
                // Update the procurement_form record.
                DB::connection('ilcdb')->table('procurement_form')
                    ->where('procurement_id', $validatedData['procurement_id'])
                    ->update([
                        'dt_submitted1' => $validatedData['dt_submitted1'] ?? null,
                        'dt_received1'  => $validatedData['dt_received1'] ?? null,
                        'dt_submitted2' => $validatedData['dt_submitted2'] ?? null,
                        'dt_received2'  => $validatedData['dt_received2'] ?? null,
                        'dt_submitted3' => $validatedData['dt_submitted3'] ?? null,
                        'dt_received3'  => $validatedData['dt_received3'] ?? null,
                        'dt_submitted4' => $validatedData['dt_submitted4'] ?? null,
                        'dt_received4'  => $validatedData['dt_received4'] ?? null,
                        'dt_submitted5' => $validatedData['dt_submitted5'] ?? null,
                        'dt_received5'  => $validatedData['dt_received5'] ?? null,
                        'dt_submitted6' => $validatedData['dt_submitted6'] ?? null,
                        'dt_received6'  => $validatedData['dt_received6'] ?? null,
                        'budget_spent'  => $validatedData['budget_spent'] ?? null,
                        'unit'          => $unit,
                        'status'        => $status,
                    ]);

                // Retrieve the updated procurement_form record.
                $record = DB::connection('ilcdb')->table('procurement_form')
                            ->where('procurement_id', $validatedData['procurement_id'])
                            ->first();

            });

            return response()->json([
                'message' => 'Procurement form updated successfully!',
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
                1 => ['appFile', 'saroFile', 'budgetFile', 'distributionFile', 'poiFile', 'researchFile'],
                2 => ['poFile', 'absFile'],
                3 => ['orsFile',],
                4 => ['attendanceFile', 'cocFile', 'photoFile', 'soaFile', 'drFile', 'dlFile'],
                5 => [],
                6 => ['dvFile'],
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
    
                    $fileName = time() . '_' . $request->file($file)->getClientOriginalName();
                    $filePath = "uploads/requirements/{$request->procurement_id}/" . $fileName;
                    $request->file($file)->move($uploadDir, $fileName);
    
                    DB::connection('ilcdb')->table('requirements')
                        ->where('procurement_id', $request->procurement_id)
                        ->where('requirement_name', $file)
                        ->delete();
    
                    DB::connection('ilcdb')->table('requirements')->insert([
                        'procurement_id'   => $request->procurement_id,
                        'requirement_name' => $file,
                        'file_path'        => $filePath,
                    ]);
    
                    $uploads[] = $file;
                } else {
                    $missingFiles[] = $file;
                }
            }
    
            \Log::info('Uploaded Files:', $uploads);
            \Log::info('Missing Files:', $missingFiles);
    
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



}


