<?php

namespace App\Http\Controllers;

use App\Models\{Employee, Payslip, Project};
use App\Services\PayslipService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PayslipController extends Controller
{
    public function index(Request $request)
    {
        $query = Payslip::with('employee', 'project');

        if ($request->month) {
            $query->where('month', $request->month);
        }

        if ($request->year) {
            $query->where('year', $request->year);
        }

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payslips = $query->latest()->paginate(30)->appends($request->query());

        return view('payslips.index', [
            'payslips' => $payslips,
            'employees' => Employee::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'filters' => [
                'month' => $request->month,
                'year' => $request->year,
                'project_id' => $request->project_id,
                'status' => $request->status,
            ],
        ]);
    }

    public function generate(Request $request, PayslipService $service)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'project_id' => 'nullable|exists:projects,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2099',
        ]);

        try {
            $employees = Employee::when($validated['employee_id'] ?? null, function ($query, $employeeId) {
                return $query->where('id', $employeeId);
            })->when($validated['project_id'] ?? null, function ($query, $projectId) {
                return $query->where('current_project_id', $projectId);
            })->get();

            $count = 0;
            foreach ($employees as $employee) {
                $projectId = $validated['project_id'] ?? $employee->current_project_id;
                $service->generate(
                    $employee,
                    $projectId,
                    $validated['month'],
                    $validated['year'],
                    auth()->id()
                );
                $count++;
            }

            return back()->with('success', "Generated payslips for {$count} employee(s).");
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating payslips: ' . $e->getMessage());
        }
    }

    public function show(Payslip $payslip)
    {
        $payslip->load('employee', 'project');

        return view('payslips.show', compact('payslip'));
    }

    public function finalize(Payslip $payslip, PayslipService $service)
    {
        try {
            // Check if already finalized for this employee/project/month/year
            $existing = Payslip::where('employee_id', $payslip->employee_id)
                ->where('project_id', $payslip->project_id)
                ->where('month', $payslip->month)
                ->where('year', $payslip->year)
                ->where('status', 'final')
                ->where('id', '!=', $payslip->id)
                ->first();

            if ($existing) {
                return back()->with('error', 'A final payslip already exists for this employee/project/month/year.');
            }

            $service->finalize($payslip);

            return back()->with('success', 'Payslip finalized successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error finalizing payslip: ' . $e->getMessage());
        }
    }

    public function pdf(Payslip $payslip)
    {
        $payslip->load('employee', 'project');

        return Pdf::loadView('payslips.pdf', compact('payslip'))
            ->download('payslip-' . $payslip->id . '.pdf');
    }
}
