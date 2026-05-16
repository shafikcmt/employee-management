<?php

namespace App\Http\Controllers;

use App\Models\{Employee, AttendanceRecord, AdvancePayment, ExtraDuty, Payslip, Project, GeneratedReport};
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $today = today();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Employee Statistics
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'active')->count();
        $inactiveEmployees = $totalEmployees - $activeEmployees;

        // Today's Attendance
        $presentToday = AttendanceRecord::whereDate('attendance_date', $today)
            ->where('status', 'present')
            ->count();
        $absentToday = AttendanceRecord::whereDate('attendance_date', $today)
            ->where('status', 'absent')
            ->count();
        $fridayToday = AttendanceRecord::whereDate('attendance_date', $today)
            ->where('status', 'friday')
            ->count();

        // Monthly Statistics
        $monthlyTotalHours = AttendanceRecord::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('hours') ?? 0;

        $monthlyExtraDutyHours = ExtraDuty::whereMonth('duty_date', $currentMonth)
            ->whereYear('duty_date', $currentYear)
            ->where('approval_status', 'approved')
            ->sum('hours_worked') ?? 0;

        // Financial Data
        $totalAdvancePaid = AdvancePayment::sum('amount') ?? 0;
        $monthlyAdvancePaid = AdvancePayment::whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->sum('amount') ?? 0;

        $approvedExtraDutyAmount = ExtraDuty::where('approval_status', 'approved')
            ->sum('amount') ?? 0;

        $monthlyApprovedExtraDutyAmount = ExtraDuty::whereMonth('duty_date', $currentMonth)
            ->whereYear('duty_date', $currentYear)
            ->where('approval_status', 'approved')
            ->sum('amount') ?? 0;

        $pendingExtraDutyAmount = ExtraDuty::where('approval_status', 'pending')
            ->sum('amount') ?? 0;

        // Payslip Data
        $monthlyGrossSalary = Payslip::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('gross_salary') ?? 0;

        $monthlyNetSalary = Payslip::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('net_salary') ?? 0;

        $draftPayslips = Payslip::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->where('status', 'draft')
            ->count();

        $finalPayslips = Payslip::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->where('status', 'final')
            ->count();

        // Extra Duty Stats
        $pendingExtraDuties = ExtraDuty::where('approval_status', 'pending')->count();
        $approvedExtraDuties = ExtraDuty::where('approval_status', 'approved')->count();
        $rejectedExtraDuties = ExtraDuty::where('approval_status', 'rejected')->count();

        // Project Data
        $totalProjects = Project::where('status', 'active')->count();

        // Reports
        $recentReports = GeneratedReport::with('generatedBy')->latest()->limit(5)->get();
        $totalReportsGenerated = GeneratedReport::count();

        return view('dashboard', [
            'totalEmployees' => $totalEmployees,
            'activeEmployees' => $activeEmployees,
            'inactiveEmployees' => $inactiveEmployees,
            'presentToday' => $presentToday,
            'absentToday' => $absentToday,
            'fridayToday' => $fridayToday,
            'monthlyTotalHours' => round($monthlyTotalHours, 2),
            'monthlyExtraDutyHours' => round($monthlyExtraDutyHours, 2),
            'totalAdvancePaid' => round($totalAdvancePaid, 2),
            'monthlyAdvancePaid' => round($monthlyAdvancePaid, 2),
            'approvedExtraDutyAmount' => round($approvedExtraDutyAmount, 2),
            'monthlyApprovedExtraDutyAmount' => round($monthlyApprovedExtraDutyAmount, 2),
            'pendingExtraDutyAmount' => round($pendingExtraDutyAmount, 2),
            'monthlyGrossSalary' => round($monthlyGrossSalary, 2),
            'monthlyNetSalary' => round($monthlyNetSalary, 2),
            'draftPayslips' => $draftPayslips,
            'finalPayslips' => $finalPayslips,
            'pendingExtraDuties' => $pendingExtraDuties,
            'approvedExtraDuties' => $approvedExtraDuties,
            'rejectedExtraDuties' => $rejectedExtraDuties,
            'totalProjects' => $totalProjects,
            'recentReports' => $recentReports,
            'totalReportsGenerated' => $totalReportsGenerated,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
        ]);
    }
}
