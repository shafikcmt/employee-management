<?php

use App\Http\Controllers\AdvancePaymentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExtraDutyController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('employees', EmployeeController::class);
    Route::resource('advance-payments', AdvancePaymentController::class);
    Route::resource('extra-duties', ExtraDutyController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('companies', CompanyController::class);
    Route::resource('designations', DesignationController::class);

    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/take', [AttendanceController::class, 'take'])->name('attendance.take');
    Route::post('attendance/bulk', [AttendanceController::class, 'bulkStore'])->name('attendance.bulk-store');
    Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('absent', [AttendanceController::class, 'absent'])->name('absent.index');

    Route::post('projects/{project}/assign-employees', [ProjectController::class, 'assignEmployees'])->name('projects.assign-employees');

    Route::get('payslips', [PayslipController::class, 'index'])->name('payslips.index');
    Route::post('payslips/generate', [PayslipController::class, 'generate'])->name('payslips.generate');
    Route::get('payslips/{payslip}', [PayslipController::class, 'show'])->name('payslips.show');
    Route::post('payslips/{payslip}/finalize', [PayslipController::class, 'finalize'])->name('payslips.finalize');
    Route::get('payslips/{payslip}/pdf', [PayslipController::class, 'pdf'])->name('payslips.pdf');

    Route::post('extra-duties/{extraDuty}/approve', [ExtraDutyController::class, 'approve'])->name('extra-duties.approve');
    Route::post('extra-duties/{extraDuty}/reject', [ExtraDutyController::class, 'reject'])->name('extra-duties.reject');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::post('reports/generate-all', [ReportController::class, 'generateAll'])->name('reports.generate-all');
    Route::get('reports/{generatedReport}/download', [ReportController::class, 'download'])->name('reports.download');

    Route::get('imports', [ImportController::class, 'index'])->name('imports.index');
    Route::post('imports/run', [ImportController::class, 'run'])->name('imports.run');
});
