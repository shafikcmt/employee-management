@extends('layouts.app')
@section('title','Take Attendance')
@section('content')
<style>
    .attendance-list-page{--teal:#0f766e;--teal-dark:#115e59;--line:#dbe3ef;--soft:#f8fafc;--ink:#0f172a;--muted:#64748b;}
    .page-card{background:#fff;border:1px solid var(--line);border-radius:18px;box-shadow:0 10px 26px rgba(15,23,42,.06);}
    .field{width:100%;border:1px solid #cbd5e1;border-radius:12px;background:#fff;padding:.68rem .82rem;font-size:.92rem;outline:none;transition:.16s ease;}
    .field:focus{border-color:#14b8a6;box-shadow:0 0 0 3px rgba(20,184,166,.14);}
    .btn{border:1px solid #cbd5e1;background:#fff;color:#0f172a;border-radius:12px;padding:.64rem .9rem;font-weight:800;font-size:.85rem;display:inline-flex;align-items:center;justify-content:center;gap:.35rem;white-space:nowrap;transition:.15s ease;}
    .btn:hover{background:#f8fafc;transform:translateY(-1px);}
    .btn-primary{background:#0f766e;border-color:#0f766e;color:#fff;}
    .btn-primary:hover{background:#115e59;}
    .btn-danger{background:#fff1f2;border-color:#fecaca;color:#991b1b;}
    .btn-warning{background:#fffbeb;border-color:#fde68a;color:#92400e;}
    .btn-blue{background:#eff6ff;border-color:#bfdbfe;color:#1d4ed8;}
    .pill{display:inline-flex;align-items:center;gap:.3rem;border-radius:999px;padding:.28rem .58rem;font-size:.72rem;font-weight:900;}
    .pill-muted{background:#f1f5f9;color:#475569;}
    .pill-present{background:#dcfce7;color:#166534;}
    .pill-absent{background:#fee2e2;color:#991b1b;}
    .pill-extra{background:#fef3c7;color:#92400e;}
    .status-mini{border:1px solid #dbe3ef;background:#fff;border-radius:10px;padding:.48rem .56rem;font-size:.76rem;font-weight:900;min-width:54px;text-align:center;transition:.14s ease;}
    .status-mini:hover{background:#f8fafc;}
    .status-mini.active[data-status="present"]{background:#0f766e;border-color:#0f766e;color:#fff;}
    .status-mini.active[data-status="absent"]{background:#dc2626;border-color:#dc2626;color:#fff;}
    .status-mini.active[data-status="friday"]{background:#f59e0b;border-color:#f59e0b;color:#fff;}
    .status-mini.active[data-status="public_holiday"]{background:#2563eb;border-color:#2563eb;color:#fff;}
    .status-mini.active[data-status="not_working"]{background:#475569;border-color:#475569;color:#fff;}
    .attendance-table-wrap{overflow:auto;border:1px solid #e2e8f0;border-radius:16px;}
    .attendance-table{width:100%;border-collapse:separate;border-spacing:0;min-width:1180px;}
    .attendance-table th{position:sticky;top:0;z-index:5;background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:.78rem .65rem;text-align:left;white-space:nowrap;}
    .attendance-table td{border-bottom:1px solid #eef2f7;padding:.65rem;vertical-align:middle;background:#fff;}
    .attendance-table tr:hover td{background:#f8fafc;}
    .attendance-table tr.is-present td:first-child{box-shadow:inset 5px 0 0 #0f766e;}
    .attendance-table tr.is-absent td:first-child{box-shadow:inset 5px 0 0 #dc2626;}
    .attendance-table tr.is-extra td:first-child{box-shadow:inset 5px 0 0 #f59e0b;}
    .employee-name{font-weight:900;color:#0f172a;line-height:1.15;}
    .employee-meta{font-size:.75rem;color:#64748b;margin-top:.2rem;}
    .small-input{border:1px solid #cbd5e1;border-radius:10px;padding:.5rem .58rem;width:100%;outline:none;background:#fff;}
    .small-input:focus{border-color:#14b8a6;box-shadow:0 0 0 3px rgba(20,184,166,.13);}
    .savebar{position:sticky;bottom:12px;z-index:30;background:rgba(255,255,255,.96);backdrop-filter:blur(10px);border:1px solid #ccfbf1;border-radius:18px;box-shadow:0 18px 45px rgba(15,23,42,.18);}
    .summary-box{border:1px solid #e2e8f0;background:#fff;border-radius:14px;padding:.75rem .9rem;}
    @media(max-width:900px){
        .attendance-table{min-width:0;border-spacing:0;}
        .attendance-table thead{display:none;}
        .attendance-table,.attendance-table tbody,.attendance-table tr,.attendance-table td{display:block;width:100%;}
        .attendance-table tr{border-bottom:1px solid #e2e8f0;padding:.8rem;background:#fff;}
        .attendance-table td{border:0;padding:.35rem 0;background:transparent!important;}
        .mobile-label{display:block;font-size:.68rem;font-weight:900;color:#64748b;text-transform:uppercase;margin-bottom:.25rem;}
        .status-actions{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:.35rem;}
    }
    @media(min-width:901px){.mobile-label{display:none}.status-actions{display:flex;gap:.35rem;}}
</style>

<div class="attendance-list-page space-y-4">
    <div class="page-card p-4 md:p-5">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <div class="pill pill-present">Daily Attendance</div>
                <h1 class="mt-2 text-2xl font-black text-slate-950">Simple list view</h1>
                <p class="mt-1 text-sm text-slate-500">Select project/date, mark status in rows, enter hours, then save once.</p>
            </div>
            <form method="get" class="grid gap-3 md:grid-cols-[260px_190px_160px] md:items-end">
                <div>
                    <label class="text-xs font-black uppercase tracking-wide text-slate-500">Project</label>
                    <select name="project_id" class="field mt-1">
                        <option value="">All Active Employees</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" @selected($projectId == $project->id)>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-black uppercase tracking-wide text-slate-500">Date</label>
                    <input class="field mt-1" type="date" name="date" value="{{ $date->toDateString() }}">
                </div>
                <button class="btn btn-primary h-[44px]">Load</button>
            </form>
        </div>
    </div>

    @if($isFriday)
        <div class="page-card border-amber-200 bg-amber-50 p-3 text-sm font-bold text-amber-800">
            Friday selected. Any employee with worked hours will be treated as Friday duty / extra duty automatically.
        </div>
    @endif

    <form method="post" action="{{ route('attendance.bulk-store') }}" id="attendanceForm" class="space-y-4">
        @csrf
        <input type="hidden" name="attendance_date" value="{{ $date->toDateString() }}">
        <input type="hidden" name="project_id" value="{{ $projectId }}">

        <div class="page-card p-4">
            <div class="grid gap-3 xl:grid-cols-[1fr_auto] xl:items-center">
                <div>
                    <h2 class="text-xl font-black text-slate-950">{{ $date->format('l, d M Y') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $employees->count() }} active employee(s) loaded.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary" onclick="markAll('present')">All Present</button>
                    <button type="button" class="btn btn-danger" onclick="markAll('absent')">All Absent</button>
                    <button type="button" class="btn btn-warning" onclick="markAll('friday')">All Friday</button>
                    <button type="button" class="btn btn-blue" onclick="markAll('public_holiday')">All PH</button>
                    <button type="button" class="btn" onclick="clearAllRows()">Clear</button>
                </div>
            </div>

            <div class="mt-4 grid gap-3 lg:grid-cols-[1fr_150px_auto] lg:items-end">
                <div>
                    <label class="text-xs font-black uppercase tracking-wide text-slate-500">Search</label>
                    <input id="employeeSearch" class="field mt-1" type="search" placeholder="Search employee, iqama, designation, project..." oninput="filterEmployees()">
                </div>
                <div>
                    <label class="text-xs font-black uppercase tracking-wide text-slate-500">Default hours</label>
                    <input id="defaultHours" class="field mt-1" type="number" step="0.01" min="0" value="8">
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="btn" onclick="showOnly('all')">Show All</button>
                    <button type="button" class="btn" onclick="showOnly('absent')">Absent</button>
                    <button type="button" class="btn" onclick="showOnly('extra')">Extra</button>
                </div>
            </div>

            <div class="mt-4 grid gap-2 md:grid-cols-5">
                <div class="summary-box"><div class="text-xs font-black uppercase text-slate-400">Total</div><div class="text-2xl font-black" id="statTotal">{{ $employees->count() }}</div></div>
                <div class="summary-box"><div class="text-xs font-black uppercase text-emerald-600">Present</div><div class="text-2xl font-black text-emerald-700" id="statPresent">0</div></div>
                <div class="summary-box"><div class="text-xs font-black uppercase text-red-600">Absent</div><div class="text-2xl font-black text-red-700" id="statAbsent">0</div></div>
                <div class="summary-box"><div class="text-xs font-black uppercase text-amber-600">Extra</div><div class="text-2xl font-black text-amber-700" id="statExtra">0</div></div>
                <div class="summary-box"><div class="text-xs font-black uppercase text-slate-400">Visible</div><div class="text-2xl font-black" id="visibleCountTop">{{ $employees->count() }}</div></div>
            </div>
        </div>

        <div class="page-card p-3 md:p-4">
            <div class="attendance-table-wrap">
                <table class="attendance-table" id="attendanceTable">
                    <thead>
                        <tr>
                            <th style="width:46px">#</th>
                            <th>Employee</th>
                            <th style="width:340px">Status</th>
                            <th style="width:95px">Hours</th>
                            <th style="width:120px">Extra</th>
                            <th style="width:105px">Rate</th>
                            <th style="width:160px">Duty Type</th>
                            <th style="width:190px">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($employees as $index => $employee)
                        @php($record = $records->get($employee->id))
                        @php($currentStatus = optional($record)->status ?? 'empty')
                        @php($isExtra = (bool) (optional($record)->is_extra_duty ?? false))
                        @php($rate = optional($record)->extra_duty_hours ? number_format((optional($record)->extra_duty_amount ?? 0) / max(optional($record)->extra_duty_hours, 1), 2, '.', '') : '')
                        @php($searchText = strtolower(trim($employee->name.' '.$employee->iqama_no.' '.optional($employee->designation)->name.' '.optional($employee->company)->name.' '.optional($employee->currentProject)->name)))
                        <tr class="attendance-row {{ $currentStatus === 'present' ? 'is-present' : '' }} {{ $currentStatus === 'absent' ? 'is-absent' : '' }} {{ $isExtra ? 'is-extra' : '' }}" data-search="{{ $searchText }}" data-row-status="{{ $currentStatus }}" data-extra="{{ $isExtra ? '1' : '0' }}">
                            <td>
                                <span class="mobile-label">No</span>
                                <span class="font-black text-slate-500">{{ $index + 1 }}</span>
                                <input type="hidden" name="employee_ids[]" value="{{ $employee->id }}">
                                <select name="status[{{ $employee->id }}]" class="status-select hidden">
                                    @foreach(['empty'=>'Empty','present'=>'Present','absent'=>'Absent','friday'=>'Friday','public_holiday'=>'Public Holiday','not_working'=>'Not Working'] as $value => $label)
                                        <option value="{{ $value }}" @selected($currentStatus === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <span class="mobile-label">Employee</span>
                                <div class="employee-name">{{ $employee->name }}</div>
                                <div class="employee-meta">
                                    Iqama: <b>{{ $employee->iqama_no ?: '-' }}</b>
                                    · {{ optional($employee->designation)->name ?: 'No designation' }}
                                    · Project: {{ optional($employee->currentProject)->name ?: '-' }}
                                </div>
                                <div class="mt-1 flex flex-wrap gap-1">
                                    <span class="status-label pill pill-muted">{{ ucfirst(str_replace('_',' ', $currentStatus)) }}</span>
                                    <span class="extra-label pill pill-extra {{ $isExtra ? '' : 'hidden' }}">Extra Duty</span>
                                </div>
                            </td>
                            <td>
                                <span class="mobile-label">Status</span>
                                <div class="status-actions">
                                    <button type="button" class="status-mini" data-status="present" onclick="setStatus(this,'present')">Present</button>
                                    <button type="button" class="status-mini" data-status="absent" onclick="setStatus(this,'absent')">Absent</button>
                                    <button type="button" class="status-mini" data-status="friday" onclick="setStatus(this,'friday')">Fri</button>
                                    <button type="button" class="status-mini" data-status="public_holiday" onclick="setStatus(this,'public_holiday')">PH</button>
                                    <button type="button" class="status-mini" data-status="not_working" onclick="setStatus(this,'not_working')">NW</button>
                                </div>
                            </td>
                            <td>
                                <span class="mobile-label">Hours</span>
                                <input class="small-input hours-input" type="number" step="0.01" min="0" name="hours[{{ $employee->id }}]" value="{{ optional($record)->hours }}" placeholder="8" oninput="hoursChanged(this)">
                            </td>
                            <td>
                                <span class="mobile-label">Extra Duty</span>
                                <label class="inline-flex cursor-pointer items-center gap-2 font-bold text-amber-800">
                                    <input class="extra-checkbox h-4 w-4" type="checkbox" name="is_extra_duty[{{ $employee->id }}]" value="1" @checked($isExtra) onchange="extraChanged(this)">
                                    Extra
                                </label>
                            </td>
                            <td>
                                <span class="mobile-label">Rate</span>
                                <input class="small-input rate-input" type="number" step="0.01" min="0" name="rate[{{ $employee->id }}]" value="{{ $rate }}" placeholder="0">
                            </td>
                            <td>
                                <span class="mobile-label">Duty Type</span>
                                <select name="extra_duty_type[{{ $employee->id }}]" class="small-input duty-type-input">
                                    @foreach(['friday'=>'Friday Duty','public_holiday'=>'Public Holiday','off_day'=>'Off Day','normal_day'=>'Normal Overtime'] as $value => $label)
                                        <option value="{{ $value }}" @selected((optional($record)->extra_duty_type ?? ($isFriday ? 'friday' : 'normal_day')) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <span class="mobile-label">Remarks</span>
                                <input class="small-input" name="remarks[{{ $employee->id }}]" value="{{ optional($record)->remarks }}" placeholder="Optional note">
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-8 text-center text-slate-500">No active employees found. Select another project or add employees first.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="savebar p-3 md:p-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="text-sm font-black text-slate-950">Ready to save?</div>
                    <div class="text-xs text-slate-500">
                        Visible: <b id="visibleCount">{{ $employees->count() }}</b>, Present: <b id="savePresent">0</b>, Absent: <b id="saveAbsent">0</b>, Extra: <b id="saveExtra">0</b>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a class="btn" href="{{ route('attendance.index', ['project_id' => $projectId, 'month' => $date->month, 'year' => $date->year]) }}">Monthly Sheet</a>
                    <a class="btn" href="{{ route('absent.index', ['project_id' => $projectId, 'month' => $date->month, 'year' => $date->year]) }}">Absent Report</a>
                    <button class="btn btn-primary px-6">Save Attendance</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
const isFriday = @json($isFriday);

function rowFromElement(element) {
    return element.closest('.attendance-row');
}

function statusText(status) {
    return {
        empty: 'Empty',
        present: 'Present',
        absent: 'Absent',
        friday: 'Friday',
        public_holiday: 'Public Holiday',
        not_working: 'Not Working'
    }[status] || 'Empty';
}

function setStatus(button, status) {
    applyStatus(rowFromElement(button), status, true);
}

function applyStatus(row, status, fromButton = false) {
    const select = row.querySelector('.status-select');
    const hours = row.querySelector('.hours-input');
    const extra = row.querySelector('.extra-checkbox');
    const dutyType = row.querySelector('.duty-type-input');
    const defaultHours = document.getElementById('defaultHours')?.value || 8;

    select.value = status;
    row.dataset.rowStatus = status;
    row.querySelectorAll('.status-mini').forEach(btn => btn.classList.toggle('active', btn.dataset.status === status));
    row.querySelector('.status-label').textContent = statusText(status);

    if (status === 'present') {
        if (!hours.value || fromButton) hours.value = defaultHours;
        if (isFriday && Number(hours.value) > 0) {
            extra.checked = true;
            dutyType.value = 'friday';
        }
    }

    if (['absent','friday','public_holiday','not_working','empty'].includes(status)) {
        hours.value = '';
        extra.checked = false;
    }

    if (status === 'friday') dutyType.value = 'friday';
    if (status === 'public_holiday') dutyType.value = 'public_holiday';

    updateRowStyle(row);
    updateStats();
}

function hoursChanged(input) {
    const row = rowFromElement(input);
    const hasHours = Number(input.value) > 0;
    const extra = row.querySelector('.extra-checkbox');
    const dutyType = row.querySelector('.duty-type-input');

    if (hasHours) {
        applyStatus(row, 'present');
        if (isFriday) {
            extra.checked = true;
            dutyType.value = 'friday';
        }
    } else if (!hasHours && row.querySelector('.status-select').value === 'present') {
        extra.checked = false;
    }

    updateRowStyle(row);
    updateStats();
}

function extraChanged(input) {
    const row = rowFromElement(input);
    const hours = row.querySelector('.hours-input');
    const dutyType = row.querySelector('.duty-type-input');
    if (input.checked) {
        if (!hours.value) hours.value = document.getElementById('defaultHours')?.value || 8;
        if (isFriday) dutyType.value = 'friday';
        applyStatus(row, 'present');
        input.checked = true;
    }
    updateRowStyle(row);
    updateStats();
}

function updateRowStyle(row) {
    const status = row.querySelector('.status-select').value;
    const extra = row.querySelector('.extra-checkbox').checked;
    const label = row.querySelector('.status-label');
    const extraLabel = row.querySelector('.extra-label');

    row.classList.toggle('is-present', status === 'present');
    row.classList.toggle('is-absent', status === 'absent');
    row.classList.toggle('is-extra', extra);
    row.dataset.extra = extra ? '1' : '0';

    label.className = 'status-label pill ' + (status === 'present' ? 'pill-present' : status === 'absent' ? 'pill-absent' : 'pill-muted');
    extraLabel.classList.toggle('hidden', !extra);
}

function markAll(status) {
    document.querySelectorAll('.attendance-row').forEach(row => {
        if (row.style.display === 'none') return;
        applyStatus(row, status, true);
    });
}

function clearAllRows() {
    document.querySelectorAll('.attendance-row').forEach(row => {
        row.querySelector('.hours-input').value = '';
        row.querySelector('.extra-checkbox').checked = false;
        applyStatus(row, 'empty');
    });
}

function filterEmployees() {
    const query = (document.getElementById('employeeSearch').value || '').toLowerCase().trim();
    let visible = 0;
    document.querySelectorAll('.attendance-row').forEach(row => {
        const matched = !query || row.dataset.search.includes(query);
        row.style.display = matched ? '' : 'none';
        if (matched) visible++;
    });
    updateVisible(visible);
}

function showOnly(type) {
    let visible = 0;
    document.querySelectorAll('.attendance-row').forEach(row => {
        const status = row.querySelector('.status-select').value;
        const extra = row.querySelector('.extra-checkbox').checked;
        const matched = type === 'all' || status === type || (type === 'extra' && extra);
        row.style.display = matched ? '' : 'none';
        if (matched) visible++;
    });
    document.getElementById('employeeSearch').value = '';
    updateVisible(visible);
}

function updateVisible(count) {
    document.getElementById('visibleCount').textContent = count;
    document.getElementById('visibleCountTop').textContent = count;
}

function updateStats() {
    let present = 0, absent = 0, extra = 0, visible = 0;
    document.querySelectorAll('.attendance-row').forEach(row => {
        if (row.style.display !== 'none') visible++;
        const status = row.querySelector('.status-select').value;
        if (status === 'present') present++;
        if (status === 'absent') absent++;
        if (row.querySelector('.extra-checkbox').checked) extra++;
    });
    document.getElementById('statPresent').textContent = present;
    document.getElementById('statAbsent').textContent = absent;
    document.getElementById('statExtra').textContent = extra;
    document.getElementById('savePresent').textContent = present;
    document.getElementById('saveAbsent').textContent = absent;
    document.getElementById('saveExtra').textContent = extra;
    updateVisible(visible);
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.attendance-row').forEach(row => {
        applyStatus(row, row.querySelector('.status-select').value || 'empty');
        if (isFriday && Number(row.querySelector('.hours-input').value) > 0) {
            row.querySelector('.extra-checkbox').checked = true;
            row.querySelector('.duty-type-input').value = 'friday';
        }
        updateRowStyle(row);
    });
    updateStats();
});
</script>
@endsection
