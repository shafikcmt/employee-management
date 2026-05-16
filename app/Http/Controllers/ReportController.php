<?php

namespace App\Http\Controllers;

use App\Models\{GeneratedReport, Project, Employee, Company, Designation};
use App\Services\ReportGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = GeneratedReport::latest()->paginate(20);

        return view('reports.index', [
            'reports' => $reports,
            'projects' => Project::orderBy('name')->get(),
            'employees' => Employee::orderBy('name')->get(),
            'companies' => Company::orderBy('name')->get(),
            'designations' => Designation::orderBy('name')->get(),
            'filters' => [
                'month' => $request->month,
                'year' => $request->year,
                'project_id' => $request->project_id,
                'employee_id' => $request->employee_id,
                'company_id' => $request->company_id,
                'designation_id' => $request->designation_id,
            ],
        ]);
    }

    public function generate(Request $request, ReportGenerationService $service)
    {
        $validated = $request->validate([
            'report_type' => 'required|string',
            'file_type' => 'required|in:pdf,excel,zip',
            'project_id' => 'nullable|exists:projects,id',
            'employee_id' => 'nullable|exists:employees,id',
            'company_id' => 'nullable|exists:companies,id',
            'designation_id' => 'nullable|exists:designations,id',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000|max:2099',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        try {
            $report = $service->generate(
                $validated['report_type'],
                $validated['file_type'],
                $validated,
                auth()->id()
            );

            return back()->with('success', 'Report generated successfully: ' . $report->report_name);
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating report: ' . $e->getMessage());
        }
    }

    public function generateAll(Request $request, ReportGenerationService $service)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'employee_id' => 'nullable|exists:employees,id',
            'company_id' => 'nullable|exists:companies,id',
            'designation_id' => 'nullable|exists:designations,id',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000|max:2099',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        try {
            $report = $service->generateAll($validated, auth()->id());

            return back()->with('success', 'All reports ZIP generated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating reports: ' . $e->getMessage());
        }
    }

    public function download(GeneratedReport $report)
    {
        if (!Storage::exists($report->file_path)) {
            return back()->with('error', 'Report file not found.');
        }

        return Storage::download($report->file_path);
    }
}
