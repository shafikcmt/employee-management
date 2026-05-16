<?php

namespace App\Http\Controllers;

use App\Models\{ExtraDuty, Employee, Project};
use Illuminate\Http\Request;

class ExtraDutyController extends Controller
{
    public function index(Request $request)
    {
        $query = ExtraDuty::with('employee', 'project');

        // Filter by approval status
        if ($request->status) {
            $query->where('approval_status', $request->status);
        }

        // Filter by employee
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by project
        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by month/year
        if ($request->month) {
            $query->whereMonth('duty_date', $request->month);
        }

        if ($request->year) {
            $query->whereYear('duty_date', $request->year);
        }

        // Filter by duty type
        if ($request->duty_type) {
            $query->where('day_type', $request->duty_type);
        }

        $extraDuties = $query->latest('duty_date')->paginate(30)->appends($request->query());

        return view('duties.index', [
            'extraDuties' => $extraDuties,
            'employees' => Employee::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'filters' => [
                'status' => $request->status,
                'employee_id' => $request->employee_id,
                'project_id' => $request->project_id,
                'month' => $request->month,
                'year' => $request->year,
                'duty_type' => $request->duty_type,
            ],
        ]);
    }

    public function create()
    {
        return view('duties.form', [
            'extraDuty' => new ExtraDuty,
            'employees' => Employee::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'project_id' => 'nullable|exists:projects,id',
            'duty_date' => 'required|date',
            'day_type' => 'required|in:friday,public_holiday,off_day,normal_day,overtime',
            'hours_worked' => 'required|numeric|min:0.01',
            'rate' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        // Calculate amount
        $validated['amount'] = round($validated['hours_worked'] * $validated['rate'], 2);
        $validated['approval_status'] = 'pending';

        ExtraDuty::create($validated);

        return redirect()->route('extra-duties.index')->with('success', 'Extra duty record created successfully.');
    }

    public function show(ExtraDuty $extraDuty)
    {
        $extraDuty->load('employee', 'project');

        return view('duties.show', compact('extraDuty'));
    }

    public function edit(ExtraDuty $extraDuty)
    {
        return view('duties.form', [
            'extraDuty' => $extraDuty,
            'employees' => Employee::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ExtraDuty $extraDuty)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'project_id' => 'nullable|exists:projects,id',
            'duty_date' => 'required|date',
            'day_type' => 'required|in:friday,public_holiday,off_day,normal_day,overtime',
            'hours_worked' => 'required|numeric|min:0.01',
            'rate' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        // Calculate amount
        $validated['amount'] = round($validated['hours_worked'] * $validated['rate'], 2);

        $extraDuty->update($validated);

        return redirect()->route('extra-duties.index')->with('success', 'Extra duty record updated successfully.');
    }

    public function destroy(ExtraDuty $extraDuty)
    {
        $extraDuty->delete();

        return back()->with('success', 'Extra duty record deleted successfully.');
    }

    public function approve(ExtraDuty $extraDuty)
    {
        $extraDuty->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Extra duty approved.');
    }

    public function reject(ExtraDuty $extraDuty)
    {
        $extraDuty->update([
            'approval_status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Extra duty rejected.');
    }
}
