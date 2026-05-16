<?php

namespace App\Services;

use App\Models\{GeneratedReport, Employee, AttendanceRecord, AdvancePayment, ExtraDuty, Payslip};
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class ReportGenerationService
{
    public function generate(string $reportType, string $fileType, array $filters, ?int $userId = null): GeneratedReport
    {
        @mkdir(storage_path('app/reports'), 0777, true);

        $timestamp = date('Ymd_His');
        $fileName = $reportType . '_' . $timestamp;
        $extension = $this->getFileExtension($fileType);
        $relativePath = 'reports/' . $fileName . '.' . $extension;
        $absolutePath = storage_path('app/' . $relativePath);

        if ($fileType === 'zip') {
            $this->generateZip($absolutePath, $filters);
        } else {
            [$headers, $rows] = $this->getReportData($reportType, $filters);

            if ($fileType === 'excel') {
                $this->generateExcel($headers, $rows, $absolutePath);
            } else {
                $this->generatePdf($reportType, $headers, $rows, $absolutePath);
            }
        }

        return GeneratedReport::create([
            'report_type' => $reportType,
            'report_name' => $this->getReportName($reportType),
            'project_id' => $filters['project_id'] ?? null,
            'employee_id' => $filters['employee_id'] ?? null,
            'month' => $filters['month'] ?? null,
            'year' => $filters['year'] ?? null,
            'filters' => $filters,
            'file_type' => $fileType,
            'file_path' => $relativePath,
            'generated_by' => $userId,
            'generated_at' => now(),
            'status' => 'completed',
        ]);
    }

    public function generateAll(array $filters, ?int $userId = null): GeneratedReport
    {
        return $this->generate('all_reports', 'zip', $filters, $userId);
    }

    private function getReportData(string $reportType, array $filters): array
    {
        $month = $filters['month'] ?? null;
        $year = $filters['year'] ?? null;
        $projectId = $filters['project_id'] ?? null;
        $employeeId = $filters['employee_id'] ?? null;
        $companyId = $filters['company_id'] ?? null;
        $designationId = $filters['designation_id'] ?? null;

        if (str_contains($reportType, 'attendance')) {
            return $this->getAttendanceData($month, $year, $projectId, $employeeId);
        }

        if ($reportType === 'absent') {
            return $this->getAbsentData($month, $year, $projectId, $employeeId);
        }

        if (str_contains($reportType, 'advance')) {
            return $this->getAdvanceData($month, $year, $projectId, $employeeId);
        }

        if (str_contains($reportType, 'extra') || str_contains($reportType, 'friday')) {
            return $this->getExtraDutyData($month, $year, $projectId, $employeeId);
        }

        if (str_contains($reportType, 'payslip') || str_contains($reportType, 'salary')) {
            return $this->getPayslipData($month, $year, $projectId, $employeeId);
        }

        return $this->getEmployeeListData($projectId, $designationId, $companyId);
    }

    private function getAttendanceData(
        ?int $month,
        ?int $year,
        ?int $projectId,
        ?int $employeeId
    ): array {
        $query = AttendanceRecord::with('employee', 'project')
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId))
            ->when($month, fn ($q) => $q->where('month', $month))
            ->when($year, fn ($q) => $q->where('year', $year));

        $headers = ['Date', 'Employee', 'Iqama', 'Project', 'Status', 'Hours', 'Remarks'];
        $rows = $query->limit(10000)->get()->map(function ($record) {
            return [
                $record->attendance_date?->format('Y-m-d') ?? '',
                $record->employee?->name ?? '',
                $record->employee?->iqama_no ?? '',
                $record->project?->name ?? '',
                ucfirst(str_replace('_', ' ', $record->status)),
                $record->hours ?? '',
                $record->remarks ?? '',
            ];
        })->toArray();

        return [$headers, $rows];
    }

    private function getAbsentData(
        ?int $month,
        ?int $year,
        ?int $projectId,
        ?int $employeeId
    ): array {
        $query = AttendanceRecord::with('employee', 'project')
            ->where('status', 'absent')
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId))
            ->when($month, fn ($q) => $q->where('month', $month))
            ->when($year, fn ($q) => $q->where('year', $year));

        $headers = ['Date', 'Employee', 'Iqama', 'Designation', 'Project', 'Remarks'];
        $rows = $query->limit(5000)->get()->map(function ($record) {
            return [
                $record->attendance_date?->format('Y-m-d') ?? '',
                $record->employee?->name ?? '',
                $record->employee?->iqama_no ?? '',
                $record->employee?->designation?->name ?? '',
                $record->project?->name ?? '',
                $record->remarks ?? '',
            ];
        })->toArray();

        return [$headers, $rows];
    }

    private function getAdvanceData(
        ?int $month,
        ?int $year,
        ?int $projectId,
        ?int $employeeId
    ): array {
        $query = AdvancePayment::with('employee', 'project')
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId))
            ->when($month, fn ($q) => $q->whereMonth('payment_date', $month))
            ->when($year, fn ($q) => $q->whereYear('payment_date', $year));

        $headers = ['Date', 'Employee', 'Iqama', 'Project', 'Amount', 'Type', 'Remarks'];
        $rows = $query->limit(5000)->get()->map(function ($record) {
            return [
                $record->payment_date?->format('Y-m-d') ?? '',
                $record->employee?->name ?? '',
                $record->employee?->iqama_no ?? '',
                $record->project?->name ?? '',
                $record->amount ?? '',
                $record->payment_type ?? '',
                $record->remarks ?? '',
            ];
        })->toArray();

        return [$headers, $rows];
    }

    private function getExtraDutyData(
        ?int $month,
        ?int $year,
        ?int $projectId,
        ?int $employeeId
    ): array {
        $query = ExtraDuty::with('employee', 'project')
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId))
            ->when($month, fn ($q) => $q->whereMonth('duty_date', $month))
            ->when($year, fn ($q) => $q->whereYear('duty_date', $year));

        $headers = ['Date', 'Employee', 'Iqama', 'Project', 'Duty Type', 'Hours', 'Rate', 'Amount', 'Status'];
        $rows = $query->limit(5000)->get()->map(function ($record) {
            return [
                $record->duty_date?->format('Y-m-d') ?? '',
                $record->employee?->name ?? '',
                $record->employee?->iqama_no ?? '',
                $record->project?->name ?? '',
                ucfirst(str_replace('_', ' ', $record->day_type)),
                $record->hours_worked ?? '',
                $record->rate ?? '',
                $record->amount ?? '',
                ucfirst($record->approval_status),
            ];
        })->toArray();

        return [$headers, $rows];
    }

    private function getPayslipData(
        ?int $month,
        ?int $year,
        ?int $projectId,
        ?int $employeeId
    ): array {
        $query = Payslip::with('employee', 'project')
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId))
            ->when($month, fn ($q) => $q->where('month', $month))
            ->when($year, fn ($q) => $q->where('year', $year));

        $headers = ['Employee', 'Iqama', 'Project', 'Normal Hours', 'Extra Hours', 'Normal Salary', 'Extra Duty Amount', 'Gross', 'Advance', 'Net', 'Status'];
        $rows = $query->limit(5000)->get()->map(function ($record) {
            return [
                $record->employee?->name ?? '',
                $record->employee?->iqama_no ?? '',
                $record->project?->name ?? '',
                $record->normal_hours ?? '',
                $record->extra_duty_hours ?? '',
                number_format($record->normal_salary, 2) ?? '',
                number_format($record->extra_duty_amount, 2) ?? '',
                number_format($record->gross_salary, 2) ?? '',
                number_format($record->advance_deduction, 2) ?? '',
                number_format($record->net_salary, 2) ?? '',
                ucfirst($record->status),
            ];
        })->toArray();

        return [$headers, $rows];
    }

    private function getEmployeeListData(
        ?int $projectId,
        ?int $designationId,
        ?int $companyId
    ): array {
        $query = Employee::with('designation', 'company', 'currentProject')
            ->when($projectId, fn ($q) => $q->where('current_project_id', $projectId))
            ->when($designationId, fn ($q) => $q->where('designation_id', $designationId))
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        $headers = ['Code', 'Name', 'Iqama', 'Passport', 'Designation', 'Company', 'Project', 'Status'];
        $rows = $query->limit(5000)->get()->map(function ($employee) {
            return [
                $employee->employee_code ?? '',
                $employee->name ?? '',
                $employee->iqama_no ?? '',
                $employee->passport_no ?? '',
                $employee->designation?->name ?? '',
                $employee->company?->name ?? '',
                $employee->currentProject?->name ?? '',
                ucfirst($employee->status),
            ];
        })->toArray();

        return [$headers, $rows];
    }

    private function generateExcel(array $headers, array $rows, string $path): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        foreach ($headers as $index => $header) {
            $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
        }

        // Add data rows
        $rowNumber = 2;
        foreach ($rows as $row) {
            foreach (array_values($row) as $index => $value) {
                $sheet->setCellValueByColumnAndRow($index + 1, $rowNumber, $value);
            }
            $rowNumber++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);
    }

    private function generatePdf(string $reportType, array $headers, array $rows, string $path): void
    {
        $pdf = Pdf::loadView('reports.table', [
            'title' => $this->getReportName($reportType),
            'headers' => $headers,
            'rows' => $rows,
        ]);

        $pdf->save($path);
    }

    private function generateZip(string $path, array $filters): void
    {
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $reportTypes = [
            'employee_list',
            'daily_attendance',
            'monthly_attendance',
            'absent_report',
            'advance_payments',
            'extra_duties',
            'payslip_report',
            'salary_summary',
        ];

        foreach ($reportTypes as $reportType) {
            try {
                [$headers, $rows] = $this->getReportData($reportType, $filters);
                $tempFile = storage_path('app/reports/' . $reportType . '_temp.xlsx');
                $this->generateExcel($headers, $rows, $tempFile);
                $zip->addFile($tempFile, basename($tempFile));
            } catch (\Exception $e) {
                // Skip if report generation fails
            }
        }

        $zip->close();
    }

    private function getFileExtension(string $fileType): string
    {
        return match ($fileType) {
            'excel' => 'xlsx',
            'zip' => 'zip',
            'pdf' => 'pdf',
            default => 'pdf',
        };
    }

    private function getReportName(string $reportType): string
    {
        return ucwords(str_replace(['_', 'all reports'], [' ', 'All Reports'], $reportType));
    }
}
