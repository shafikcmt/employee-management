@extends('layouts.app')
@section('title','Reports Center')
@section('content')

<style>
    .report-filter-card {
        @apply bg-white rounded-2xl p-6 border border-slate-100 mb-6;
    }
    
    .filter-group {
        @apply mb-4;
    }
    
    .filter-label {
        @apply text-xs font-bold uppercase tracking-wide text-slate-600 mb-2 block;
    }
    
    .filter-input {
        @apply w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-50 outline-none transition;
    }
    
    .report-type-grid {
        @apply grid gap-4 md:grid-cols-2 lg:grid-cols-3;
    }
    
    .report-card {
        @apply bg-white rounded-2xl p-5 border border-slate-100 hover:border-teal-300 hover:shadow-md transition-all cursor-pointer;
    }
    
    .report-card-icon {
        @apply text-3xl mb-3;
    }
    
    .report-card-title {
        @apply font-bold text-slate-900 mb-1;
    }
    
    .report-card-desc {
        @apply text-xs text-slate-600 mb-4;
    }
    
    .report-actions {
        @apply flex gap-2 flex-wrap;
    }
    
    .report-btn {
        @apply px-3 py-1.5 rounded-lg text-xs font-bold transition-all inline-block;
    }
    
    .report-btn-pdf {
        @apply bg-red-100 text-red-700 hover:bg-red-200;
    }
    
    .report-btn-excel {
        @apply bg-emerald-100 text-emerald-700 hover:bg-emerald-200;
    }
    
    .report-btn-zip {
        @apply bg-blue-100 text-blue-700 hover:bg-blue-200;
    }
    
    .generate-all-btn {
        @apply w-full bg-gradient-to-r from-teal-600 to-teal-700 text-white px-6 py-4 rounded-xl font-bold text-lg hover:from-teal-700 hover:to-teal-800 transition-all shadow-md hover:shadow-lg;
    }
</style>

<!-- Filters Section -->
<div class="report-filter-card">
    <h2 class="text-xl font-bold text-slate-900 mb-4">Report Filters</h2>
    
    <form method="get" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <div class="filter-group">
            <label class="filter-label">Month</label>
            <select name="month" class="filter-input">
                <option value="">All Months</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected(request('month') == $m)>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">Year</label>
            <input type="number" name="year" class="filter-input" value="{{ request('year') ?: now()->year }}" min="2000" max="2099">
        </div>

        <div class="filter-group">
            <label class="filter-label">Project</label>
            <select name="project_id" class="filter-input">
                <option value="">All Projects</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" @selected(request('project_id') == $project->id)>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">Employee</label>
            <select name="employee_id" class="filter-input">
                <option value="">All Employees</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" @selected(request('employee_id') == $employee->id)>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">Company</label>
            <select name="company_id" class="filter-input">
                <option value="">All Companies</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" @selected(request('company_id') == $company->id)>
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">Designation</label>
            <select name="designation_id" class="filter-input">
                <option value="">All Designations</option>
                @foreach($designations as $designation)
                    <option value="{{ $designation->id }}" @selected(request('designation_id') == $designation->id)>
                        {{ $designation->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">&nbsp;</label>
            <button type="submit" class="w-full px-4 py-2.5 bg-teal-600 text-white font-bold rounded-lg hover:bg-teal-700 transition">
                Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- Report Types Section -->
<div class="bg-white rounded-2xl p-6 border border-slate-100 mb-6">
    <h2 class="text-xl font-bold text-slate-900 mb-6">Available Reports</h2>
    
    <div class="report-type-grid">
        <!-- Employee List Report -->
        <div class="report-card">
            <div class="report-card-icon">👥</div>
            <div class="report-card-title">Employee List</div>
            <div class="report-card-desc">Complete employee directory with details</div>
            <form method="post" action="{{ route('reports.generate') }}" class="report-actions">
                @csrf
                <input type="hidden" name="report_type" value="employee_list">
                <button type="submit" name="file_type" value="pdf" class="report-btn report-btn-pdf">PDF</button>
                <button type="submit" name="file_type" value="excel" class="report-btn report-btn-excel">Excel</button>
            </form>
        </div>

        <!-- Attendance Report -->
        <div class="report-card">
            <div class="report-card-icon">📋</div>
            <div class="report-card-title">Daily Attendance</div>
            <div class="report-card-desc">Day-wise attendance records</div>
            <form method="post" action="{{ route('reports.generate') }}" class="report-actions">
                @csrf
                <input type="hidden" name="report_type" value="daily_attendance">
                <button type="submit" name="file_type" value="pdf" class="report-btn report-btn-pdf">PDF</button>
                <button type="submit" name="file_type" value="excel" class="report-btn report-btn-excel">Excel</button>
            </form>
        </div>

        <!-- Monthly Attendance -->
        <div class="report-card">
            <div class="report-card-icon">📅</div>
            <div class="report-card-title">Monthly Attendance</div>
            <div class="report-card-desc">Month-wise attendance summary</div>
            <form method="post" action="{{ route('reports.generate') }}" class="report-actions">
                @csrf
                <input type="hidden" name="report_type" value="monthly_attendance">
                <button type="submit" name="file_type" value="pdf" class="report-btn report-btn-pdf">PDF</button>
                <button type="submit" name="file_type" value="excel" class="report-btn report-btn-excel">Excel</button>
            </form>
        </div>

        <!-- Absent Report -->
        <div class="report-card">
            <div class="report-card-icon">❌</div>
            <div class="report-card-title">Absent Report</div>
            <div class="report-card-desc">All absent records and details</div>
            <form method="post" action="{{ route('reports.generate') }}" class="report-actions">
                @csrf
                <input type="hidden" name="report_type" value="absent">
                <button type="submit" name="file_type" value="pdf" class="report-btn report-btn-pdf">PDF</button>
                <button type="submit" name="file_type" value="excel" class="report-btn report-btn-excel">Excel</button>
            </form>
        </div>

        <!-- Advance Payment Report -->
        <div class="report-card">
            <div class="report-card-icon">💵</div>
            <div class="report-card-title">Advance Payments</div>
            <div class="report-card-desc">All advance payment records</div>
            <form method="post" action="{{ route('reports.generate') }}" class="report-actions">
                @csrf
                <input type="hidden" name="report_type" value="advance_payments">
                <button type="submit" name="file_type" value="pdf" class="report-btn report-btn-pdf">PDF</button>
                <button type="submit" name="file_type" value="excel" class="report-btn report-btn-excel">Excel</button>
            </form>
        </div>

        <!-- Extra Duty Report -->
        <div class="report-card">
            <div class="report-card-icon">⏱️</div>
            <div class="report-card-title">Extra Duty Report</div>
            <div class="report-card-desc">Friday duty, overtime and extras</div>
            <form method="post" action="{{ route('reports.generate') }}" class="report-actions">
                @csrf
                <input type="hidden" name="report_type" value="extra_duties">
                <button type="submit" name="file_type" value="pdf" class="report-btn report-btn-pdf">PDF</button>
                <button type="submit" name="file_type" value="excel" class="report-btn report-btn-excel">Excel</button>
            </form>
        </div>

        <!-- Payslip Report -->
        <div class="report-card">
            <div class="report-card-icon">📝</div>
            <div class="report-card-title">Payslip Report</div>
            <div class="report-card-desc">Complete payslip details</div>
            <form method="post" action="{{ route('reports.generate') }}" class="report-actions">
                @csrf
                <input type="hidden" name="report_type" value="payslip_report">
                <button type="submit" name="file_type" value="pdf" class="report-btn report-btn-pdf">PDF</button>
                <button type="submit" name="file_type" value="excel" class="report-btn report-btn-excel">Excel</button>
            </form>
        </div>

        <!-- Salary Summary -->
        <div class="report-card">
            <div class="report-card-icon">💰</div>
            <div class="report-card-title">Salary Summary</div>
            <div class="report-card-desc">Salary totals and deductions</div>
            <form method="post" action="{{ route('reports.generate') }}" class="report-actions">
                @csrf
                <input type="hidden" name="report_type" value="salary_summary">
                <button type="submit" name="file_type" value="pdf" class="report-btn report-btn-pdf">PDF</button>
                <button type="submit" name="file_type" value="excel" class="report-btn report-btn-excel">Excel</button>
            </form>
        </div>
    </div>
</div>

<!-- Generate All Reports -->
<form method="post" action="{{ route('reports.generate-all') }}" class="mb-6">
    @csrf
    <button type="submit" class="generate-all-btn">
        📦 Generate All Reports ZIP
    </button>
    <p class="text-xs text-slate-600 mt-2 text-center">
        Generate all report types in a single ZIP file with current filters applied
    </p>
</form>

<!-- Report History -->
@if($reports->count() > 0)
    <div class="bg-white rounded-2xl p-6 border border-slate-100">
        <h2 class="text-xl font-bold text-slate-900 mb-4">Report History</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-200">
                    <tr>
                        <th class="text-left py-3 px-4 font-bold text-slate-600">Report Name</th>
                        <th class="text-left py-3 px-4 font-bold text-slate-600">Type</th>
                        <th class="text-left py-3 px-4 font-bold text-slate-600">Generated</th>
                        <th class="text-left py-3 px-4 font-bold text-slate-600">By</th>
                        <th class="text-center py-3 px-4 font-bold text-slate-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($reports as $report)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $report->report_name }}</div>
                                @if($report->month || $report->project_id)
                                    <div class="text-xs text-slate-500 mt-1">
                                        @if($report->month)
                                            {{ date('F Y', mktime(0, 0, 0, $report->month, 1, $report->year)) }}
                                        @endif
                                        @if($report->project_id)
                                            · {{ $report->project?->name }}
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs">
                                    {{ strtoupper($report->file_type) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-slate-600">
                                {{ $report->generated_at?->diffForHumans() }}
                            </td>
                            <td class="py-3 px-4 text-sm text-slate-600">
                                {{ $report->generatedBy?->name ?? 'System' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('reports.download', $report) }}" 
                                   class="inline-block px-3 py-1.5 rounded-lg bg-teal-100 text-teal-700 hover:bg-teal-200 font-bold text-xs transition">
                                    Download
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    </div>
@endif

@endsection
