<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ExcelImportService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function index()
    {
        return view('imports.index', [
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    public function run(Request $request, ExcelImportService $importService)
    {
        $validated = $request->validate([
            'advance_file' => 'nullable|file|mimes:xlsx,xls,csv',
            'timesheet_file' => 'nullable|file|mimes:xlsx,xls,csv',
            'project_id' => 'nullable|exists:projects,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2099',
        ]);

        try {
            // Store uploaded files
            $advanceFilePath = $request->file('advance_file')?->store('imports');
            $timesheetFilePath = $request->file('timesheet_file')?->store('imports');

            // Get project name or use default
            $project = Project::find($validated['project_id'])?->name ?? 'Default Project';

            // Run import
            $result = $importService->import(
                $advanceFilePath ? storage_path('app/' . $advanceFilePath) : null,
                $timesheetFilePath ? storage_path('app/' . $timesheetFilePath) : null,
                $project,
                (int) $validated['month'],
                (int) $validated['year'],
                auth()->id()
            );

            // Clean up temporary files
            if ($advanceFilePath) {
                @unlink(storage_path('app/' . $advanceFilePath));
            }
            if ($timesheetFilePath) {
                @unlink(storage_path('app/' . $timesheetFilePath));
            }

            $message = "Import completed successfully!\n";
            if ($result['advance']['total'] > 0) {
                $message .= "Advance payments: {$result['advance']['success']}/{$result['advance']['total']} succeeded\n";
            }
            if ($result['timesheet']['total'] > 0) {
                $message .= "Timesheet records: {$result['timesheet']['success']}/{$result['timesheet']['total']} succeeded";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Import error: ' . $e->getMessage());
        }
    }
}
