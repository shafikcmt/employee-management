<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\EmployeeProjectAssignment;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::withCount('employees')
            ->when($request->search, fn ($query, $value) => $query->where('name', 'like', '%' . $value . '%'))
            ->when($request->status, fn ($query, $value) => $query->where('status', $value))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('projects.index', [
            'projects' => $projects,
            'search' => $request->search,
            'status' => $request->status,
        ]);
    }

    public function create()
    {
        return view('projects.form', ['project' => new Project]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:projects,name'],
            'code' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'remarks' => ['nullable', 'string'],
        ]);

        $project = Project::create($data);

        return redirect()->route('projects.show', $project)->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->loadCount('employees');

        $employees = Employee::with(['designation', 'company'])
            ->where('current_project_id', $project->id)
            ->orderBy('name')
            ->paginate(20);

        $today = today();
        $summary = [
            'present_today' => AttendanceRecord::where('project_id', $project->id)->whereDate('attendance_date', $today)->where('status', 'present')->count(),
            'absent_today' => AttendanceRecord::where('project_id', $project->id)->whereDate('attendance_date', $today)->where('status', 'absent')->count(),
            'monthly_hours' => AttendanceRecord::where('project_id', $project->id)->whereMonth('attendance_date', now()->month)->whereYear('attendance_date', now()->year)->sum('hours'),
            'extra_duty_hours' => AttendanceRecord::where('project_id', $project->id)->whereMonth('attendance_date', now()->month)->whereYear('attendance_date', now()->year)->sum('extra_duty_hours'),
        ];

        $availableEmployees = Employee::where(function ($query) use ($project) {
                $query->whereNull('current_project_id')->orWhere('current_project_id', '!=', $project->id);
            })
            ->orderBy('name')
            ->limit(200)
            ->get();

        return view('projects.show', [
            'project' => $project,
            'employees' => $employees,
            'availableEmployees' => $availableEmployees,
            'summary' => $summary,
        ]);
    }

    public function edit(Project $project)
    {
        return view('projects.form', ['project' => $project]);
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:projects,name,' . $project->id],
            'code' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'remarks' => ['nullable', 'string'],
        ]);

        $project->update($data);

        return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    public function assignEmployees(Request $request, Project $project)
    {
        $data = $request->validate([
            'employee_ids' => ['required', 'array'],
            'employee_ids.*' => ['exists:employees,id'],
        ]);

        foreach ($data['employee_ids'] as $employeeId) {
            Employee::whereKey($employeeId)->update(['current_project_id' => $project->id]);

            EmployeeProjectAssignment::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'project_id' => $project->id,
                    'status' => 'active',
                ],
                [
                    'start_date' => today(),
                    'remarks' => 'Assigned from project page',
                ]
            );
        }

        return back()->with('success', count($data['employee_ids']) . ' employee(s) assigned to this project.');
    }
}
