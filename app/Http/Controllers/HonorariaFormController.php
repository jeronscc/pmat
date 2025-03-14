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
                return response()->json(['success' => false, 'message' => 'Procurement ID is missing.'], 400);
            }

            $requiredFiles = ['orsFile', 'dvFile', 'contractFile', 'classificationFile', 'reportFile', 'attendanceFile', 'resumeFile', 'govidFile', 'payslipFile', 'bankFile', 'certFile'];
            $uploads = [];

            foreach ($requiredFiles as $file) {
                if ($request->hasFile($file)) {
                    $request->validate([$file => 'file|max:5120|mimes:pdf,doc,docx,jpg,png']);
                    
                    $uploadDir = public_path("uploads/requirements/{$request->procurement_id}");
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileName = time() . '_' . $request->file($file)->getClientOriginalName();
                    $filePath = "uploads/requirements/{$request->procurement_id}/" . $fileName;
                    $request->file($file)->move($uploadDir, $fileName);

                    DB::connection('ilcdb')->table('requirements')->updateOrInsert(
                        ['procurement_id' => $request->procurement_id, 'requirement_name' => $file],
                        ['file_path' => $filePath]
                    );

                    $uploads[] = $file;
                }
            }

            if (empty($uploads)) {
                return response()->json(['success' => false, 'message' => 'No files uploaded.'], 400);
            }

            return response()->json(['success' => true, 'message' => 'Files uploaded successfully: ' . implode(', ', $uploads)]);

        } catch (\Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function getUploadedFiles($procurement_id)
    {
        try {
            $files = DB::connection('ilcdb')->table('requirements')
                ->where('procurement_id', $procurement_id)
                ->pluck('file_path', 'requirement_name');

            return response()->json(['success' => true, 'files' => $files]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch uploaded files: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}