<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\ExtraDuty;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) ($request->month ?: now()->month);
        $year = (int) ($request->year ?: now()->year);
        $projectId = $request->project_id;

        $employees = Employee::with(['designation', 'company', 'currentProject'])
            ->when($projectId, fn ($query) => $query->where('current_project_id', $projectId))
            ->orderBy('name')
            ->get();

        $records = AttendanceRecord::where('month', $month)
            ->where('year', $year)
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->get()
            ->groupBy(fn ($record) => $record->employee_id . '_' . $record->attendance_date->day);

        return view('attendance.index', [
            'employees' => $employees,
            'records' => $records,
            'days' => range(1, cal_days_in_month(CAL_GREGORIAN, $month, $year)),
            'month' => $month,
            'year' => $year,
            'projects' => Project::orderBy('name')->get(),
            'pid' => $projectId,
        ]);
    }

    public function take(Request $request)
    {
        $date = Carbon::parse($request->date ?: today());
        $projectId = $request->project_id;

        $projects = Project::orderBy('name')->get();

        $employees = Employee::with(['designation', 'company', 'currentProject'])
            ->when($projectId, fn ($query) => $query->where('current_project_id', $projectId))
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $records = AttendanceRecord::whereDate('attendance_date', $date)
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->get()
            ->keyBy('employee_id');

        return view('attendance.take', [
            'date' => $date,
            'projectId' => $projectId,
            'projects' => $projects,
            'employees' => $employees,
            'records' => $records,
            'isFriday' => $date->isFriday(),
        ]);
    }

    public function bulkStore(Request $request)
    {
        $data = $request->validate([
            'attendance_date' => ['required', 'date'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'employee_ids' => ['required', 'array'],
            'employee_ids.*' => ['required', 'exists:employees,id'],
            'status' => ['array'],
            'hours' => ['array'],
            'rate' => ['array'],
            'remarks' => ['array'],
            'is_extra_duty' => ['array'],
            'extra_duty_type' => ['array'],
        ]);

        $date = Carbon::parse($data['attendance_date']);
        $selectedProjectId = $data['project_id'] ?? null;
        $employees = Employee::whereIn('id', $data['employee_ids'])->get()->keyBy('id');
        $saved = 0;

        foreach ($data['employee_ids'] as $employeeId) {
            $employee = $employees->get((int) $employeeId);
            if (! $employee) {
                continue;
            }

            $status = $data['status'][$employeeId] ?? 'empty';
            $hours = $this->numberOrNull($data['hours'][$employeeId] ?? null);
            $rate = $this->numberOrZero($data['rate'][$employeeId] ?? null);
            $remarks = $data['remarks'][$employeeId] ?? null;
            $projectId = $selectedProjectId ?: $employee->current_project_id;

            $hasHours = $hours !== null && $hours > 0;
            $extraChecked = isset($data['is_extra_duty'][$employeeId]);
            $autoFridayDuty = $date->isFriday() && $hasHours;
            $isExtraDuty = $extraChecked || $autoFridayDuty;

            if ($isExtraDuty && $hasHours) {
                $status = 'present';
            }

            if (! in_array($status, ['present', 'absent', 'friday', 'public_holiday', 'not_working', 'empty'], true)) {
                $status = 'empty';
            }

            if ($status !== 'present') {
                $hours = null;
            }

            $extraDutyType = null;
            if ($isExtraDuty && $hasHours) {
                $extraDutyType = $data['extra_duty_type'][$employeeId] ?? null;
                if (! $extraDutyType) {
                    $extraDutyType = $date->isFriday() ? 'friday' : 'normal_day';
                }
            }

            $extraAmount = $isExtraDuty && $hasHours ? round($hours * $rate, 2) : null;

            AttendanceRecord::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'project_id' => $projectId,
                    'attendance_date' => $date->toDateString(),
                ],
                [
                    'month' => $date->month,
                    'year' => $date->year,
                    'status' => $status,
                    'hours' => $hours,
                    'raw_value' => $this->rawValueFor($status, $hours),
                    'is_extra_duty' => (bool) ($isExtraDuty && $hasHours),
                    'extra_duty_type' => $extraDutyType,
                    'extra_duty_hours' => $isExtraDuty && $hasHours ? $hours : null,
                    'extra_duty_amount' => $extraAmount,
                    'remarks' => $remarks,
                    'source_sheet' => 'manual_attendance_page',
                ]
            );

            if ($isExtraDuty && $hasHours) {
                ExtraDuty::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'project_id' => $projectId,
                        'duty_date' => $date->toDateString(),
                    ],
                    [
                        'day_type' => $extraDutyType ?: 'normal_day',
                        'hours_worked' => $hours,
                        'rate' => $rate,
                        'amount' => $extraAmount ?: 0,
                        'approval_status' => 'pending',
                        'remarks' => $remarks,
                        'source_sheet' => 'manual_attendance_page',
                    ]
                );
            } else {
                ExtraDuty::where('employee_id', $employee->id)
                    ->where('project_id', $projectId)
                    ->whereDate('duty_date', $date)
                    ->where('source_sheet', 'manual_attendance_page')
                    ->delete();
            }

            $saved++;
        }

        return redirect()
            ->route('attendance.take', ['date' => $date->toDateString(), 'project_id' => $selectedProjectId])
            ->with('success', $saved . ' attendance rows saved successfully.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'attendance_date' => ['required', 'date'],
            'status' => ['required'],
            'hours' => ['nullable', 'numeric'],
            'is_extra_duty' => ['nullable'],
        ]);

        $date = Carbon::parse($data['attendance_date']);
        $hours = $this->numberOrNull($data['hours'] ?? null);

        AttendanceRecord::updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'project_id' => $data['project_id'] ?? null,
                'attendance_date' => $date->toDateString(),
            ],
            $data + [
                'month' => $date->month,
                'year' => $date->year,
                'raw_value' => $this->rawValueFor($data['status'], $hours),
            ]
        );

        return back()->with('success', 'Attendance saved');
    }

    public function absent(Request $request)
    {
        $items = AttendanceRecord::with(['employee', 'project'])
            ->where('status', 'absent')
            ->when($request->project_id, fn ($query, $value) => $query->where('project_id', $value))
            ->when($request->month, fn ($query, $value) => $query->where('month', $value))
            ->when($request->year, fn ($query, $value) => $query->where('year', $value))
            ->latest('attendance_date')
            ->paginate(50)
            ->withQueryString();

        return view('attendance.absent', [
            'items' => $items,
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    private function numberOrNull($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (float) $value : null;
    }

    private function numberOrZero($value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return is_numeric($value) ? (float) $value : 0;
    }

    private function rawValueFor(string $status, ?float $hours): string
    {
        if ($status === 'present' && $hours !== null) {
            return (string) $hours;
        }

        return match ($status) {
            'absent' => 'A',
            'friday' => 'Fri',
            'public_holiday' => 'PH',
            'not_working' => 'NW',
            default => '',
        };
    }
}
