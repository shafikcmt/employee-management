<?php

namespace App\Http\Controllers;

use App\Models\{Employee, Company, Project, Designation};
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('company', 'designation', 'currentProject');

        // Search by name, iqama, or employee code
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('iqama_no', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%")
                  ->orWhere('passport_no', 'like', "%{$search}%");
            });
        }

        // Filter by project
        if ($request->project_id) {
            $query->where('current_project_id', $request->project_id);
        }

        // Filter by company
        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by designation
        if ($request->designation_id) {
            $query->where('designation_id', $request->designation_id);
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('name')->paginate(25)->appends($request->query());

        return view('employees.index', [
            'employees' => $employees,
            'companies' => Company::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'designations' => Designation::orderBy('name')->get(),
            'filters' => [
                'search' => $request->search,
                'project_id' => $request->project_id,
                'company_id' => $request->company_id,
                'designation_id' => $request->designation_id,
                'status' => $request->status,
            ],
        ]);
    }

    public function create()
    {
        return view('employees.form', [
            'employee' => new Employee,
            'companies' => Company::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'designations' => Designation::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validate($request);

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load([
            'company',
            'designation',
            'currentProject',
            'attendanceRecords',
            'advancePayments',
            'extraDuties',
            'payslips',
        ]);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('employees.form', [
            'employee' => $employee,
            'companies' => Company::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'designations' => Designation::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $this->validate($request);

        $employee->update($validated);

        return redirect()->route('employees.show', $employee)->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    private function validate(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'employee_code' => 'nullable|string|max:255|unique:employees,employee_code,' . ($request->route('employee')?->id ?? 'NULL'),
            'iqama_no' => 'nullable|string|max:255|unique:employees,iqama_no,' . ($request->route('employee')?->id ?? 'NULL'),
            'passport_no' => 'nullable|string|max:255',
            'designation_id' => 'nullable|exists:designations,id',
            'company_id' => 'nullable|exists:companies,id',
            'current_project_id' => 'nullable|exists:projects,id',
            'shift' => 'nullable|string',
            'accommodation' => 'nullable|string',
            'doj' => 'nullable|date',
            'phone' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'monthly_salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'remarks' => 'nullable|string',
        ]);
    }
}
