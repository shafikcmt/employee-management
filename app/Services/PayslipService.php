<?php

namespace App\Services;

use App\Models\{Employee, AttendanceRecord, AdvancePayment, ExtraDuty, Payslip};
use Illuminate\Support\Facades\DB;

class PayslipService
{
    public function generate(Employee $employee, ?int $projectId, int $month, int $year, ?int $userId = null): Payslip
    {
        return DB::transaction(function () use ($employee, $projectId, $month, $year, $userId) {
            // Get attendance records for the employee for this month/year
            $attendanceQuery = AttendanceRecord::where('employee_id', $employee->id)
                ->where('month', $month)
                ->where('year', $year);

            if ($projectId) {
                $attendanceQuery->where('project_id', $projectId);
            }

            // Calculate normal hours (excluding extra duty)
            $normalHours = (clone $attendanceQuery)
                ->where('status', 'present')
                ->where('is_extra_duty', false)
                ->sum('hours') ?? 0;

            // Calculate days worked
            $daysWorked = (clone $attendanceQuery)
                ->where('status', 'present')
                ->count();

            // Calculate absent days
            $absentDays = (clone $attendanceQuery)
                ->where('status', 'absent')
                ->count();

            // Get approved extra duty hours and amount for this month/year
            $extraDutyQuery = ExtraDuty::where('employee_id', $employee->id)
                ->whereMonth('duty_date', $month)
                ->whereYear('duty_date', $year)
                ->where('approval_status', 'approved');

            if ($projectId) {
                $extraDutyQuery->where('project_id', $projectId);
            }

            $extraDutyHours = $extraDutyQuery->sum('hours_worked') ?? 0;
            $extraDutyAmount = $extraDutyQuery->sum('amount') ?? 0;

            // Get advance payments for this month/year
            $advanceQuery = AdvancePayment::where('employee_id', $employee->id)
                ->whereMonth('payment_date', $month)
                ->whereYear('payment_date', $year);

            if ($projectId) {
                $advanceQuery->where('project_id', $projectId);
            }

            $advanceDeduction = $advanceQuery->sum('amount') ?? 0;

            // Calculate salary
            $hourlyRate = $employee->hourly_rate ?? 0;
            $monthlySalary = $employee->monthly_salary ?? 0;

            // If monthly salary is set, use it; otherwise calculate from hourly rate
            $normalSalary = $monthlySalary > 0 ? $monthlySalary : ($normalHours * $hourlyRate);

            // Gross salary = normal salary + extra duty amount
            $grossSalary = $normalSalary + $extraDutyAmount;

            // Net salary = gross salary - advance deduction
            $netSalary = $grossSalary - $advanceDeduction;

            // Create or update payslip
            return Payslip::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'project_id' => $projectId,
                    'month' => $month,
                    'year' => $year,
                    'status' => 'draft',
                ],
                [
                    'total_hours' => $normalHours + $extraDutyHours,
                    'normal_hours' => $normalHours,
                    'extra_duty_hours' => $extraDutyHours,
                    'days_worked' => $daysWorked,
                    'absent_days' => $absentDays,
                    'hourly_rate' => $hourlyRate,
                    'monthly_salary' => $monthlySalary,
                    'normal_salary' => round($normalSalary, 2),
                    'extra_duty_amount' => round($extraDutyAmount, 2),
                    'gross_salary' => round($grossSalary, 2),
                    'advance_deduction' => round($advanceDeduction, 2),
                    'net_salary' => round($netSalary, 2),
                    'generated_by' => $userId,
                    'generated_at' => now(),
                ]
            );
        });
    }

    public function finalize(Payslip $payslip): Payslip
    {
        $payslip->update(['status' => 'final']);

        return $payslip;
    }

    public function generateBatch(array $employees, ?int $projectId, int $month, int $year, ?int $userId = null): array
    {
        $generated = [];

        foreach ($employees as $employee) {
            $payslip = $this->generate($employee, $projectId, $month, $year, $userId);
            $generated[] = $payslip;
        }

        return $generated;
    }
}
