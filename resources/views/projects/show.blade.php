@extends('layouts.app')
@section('title',$project->name)
@section('content')
<style>
    .project-show-hero{background:linear-gradient(135deg,#0f766e,#14b8a6 55%,#0ea5e9);border-radius:28px;color:white;box-shadow:0 20px 42px rgba(20,184,166,.18);}
    .soft-card{border:1px solid #e2e8f0;border-radius:24px;background:#fff;box-shadow:0 14px 32px rgba(15,23,42,.06);} 
    .quick-tile{display:flex;align-items:center;gap:.8rem;border:1px solid #e2e8f0;border-radius:20px;background:#fff;padding:1rem;font-weight:900;transition:.16s ease;}
    .quick-tile:hover{background:#f0fdfa;border-color:#5eead4;color:#0f766e;transform:translateY(-1px);} 
    .avatar-dot{height:42px;width:42px;border-radius:16px;background:#0f766e;color:white;display:flex;align-items:center;justify-content:center;font-weight:900;}
</style>

<div class="space-y-5">
    <section class="project-show-hero p-5 md:p-7">
        <div class="flex flex-wrap items-start justify-between gap-5">
            <div>
                <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black ring-1 ring-white/25">Project Workspace</span>
                <h2 class="mt-3 text-2xl font-black md:text-4xl">{{ $project->name }}</h2>
                <p class="mt-2 text-sm text-teal-50">{{ $project->code ?: 'No code' }} · {{ $project->location ?: 'No location' }}</p>
                @if($project->remarks)
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-white/90">{{ $project->remarks }}</p>
                @endif
            </div>
            <span class="badge {{ $project->status === 'active' ? 'bg-white text-teal-700' : 'bg-slate-100 text-slate-700' }}">{{ ucfirst($project->status) }}</span>
        </div>

        <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <a class="rounded-2xl bg-white px-4 py-3 text-center text-sm font-black text-teal-700 shadow" href="{{ route('attendance.take', ['project_id' => $project->id]) }}">Take Today Attendance</a>
            <a class="rounded-2xl bg-white/15 px-4 py-3 text-center text-sm font-black text-white ring-1 ring-white/25" href="{{ route('attendance.index', ['project_id' => $project->id, 'month' => now()->month, 'year' => now()->year]) }}">Monthly Sheet</a>
            <a class="rounded-2xl bg-white/15 px-4 py-3 text-center text-sm font-black text-white ring-1 ring-white/25" href="{{ route('absent.index', ['project_id' => $project->id, 'month' => now()->month, 'year' => now()->year]) }}">Absent Report</a>
            <a class="rounded-2xl bg-amber-400 px-4 py-3 text-center text-sm font-black text-amber-950 shadow" href="{{ route('projects.edit', $project) }}">Edit Project</a>
        </div>
    </section>

    <div class="grid gap-4 md:grid-cols-4">
        @foreach([
            'Employees' => $project->employees_count,
            'Present Today' => $summary['present_today'],
            'Absent Today' => $summary['absent_today'],
            'Monthly Hours' => $summary['monthly_hours'],
        ] as $label => $value)
            <div class="soft-card p-5">
                <div class="text-xs font-black uppercase text-slate-400">{{ $label }}</div>
                <div class="mt-2 text-3xl font-black text-slate-950">{{ number_format($value, is_float($value + 0) ? 2 : 0) }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid gap-4 xl:grid-cols-[1fr_360px]">
        <section class="soft-card p-5">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-black text-slate-950">Assigned Employees</h2>
                    <p class="mt-1 text-sm text-slate-500">Use this list to quickly open employee profile or take attendance for this project.</p>
                </div>
                <a class="btn-primary rounded-2xl" href="{{ route('attendance.take', ['project_id' => $project->id]) }}">Take Attendance</a>
            </div>

            <input class="input mb-4 w-full rounded-2xl" id="projectEmployeeSearch" placeholder="Search assigned employee..." oninput="filterProjectEmployees()">

            <div class="grid gap-3 md:grid-cols-2" id="projectEmployeeGrid">
                @forelse($employees as $employee)
                    <a class="employee-mini rounded-2xl border p-4 transition hover:border-teal-300 hover:bg-teal-50" data-search="{{ strtolower($employee->name.' '.$employee->iqama_no.' '.optional($employee->designation)->name.' '.optional($employee->company)->name) }}" href="{{ route('employees.show', $employee) }}">
                        <div class="flex items-start gap-3">
                            <div class="avatar-dot">{{ strtoupper(substr($employee->name,0,1)) }}</div>
                            <div class="min-w-0 flex-1">
                                <div class="truncate font-black text-slate-950">{{ $employee->name }}</div>
                                <div class="mt-1 text-xs text-slate-500">Iqama: {{ $employee->iqama_no ?: '-' }}</div>
                                <div class="mt-1 flex flex-wrap gap-1">
                                    <span class="badge bg-slate-100 text-slate-700">{{ optional($employee->designation)->name ?: 'No designation' }}</span>
                                    <span class="badge bg-emerald-100 text-emerald-700">{{ ucfirst($employee->status) }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-2xl border p-6 text-center text-slate-500 md:col-span-2">No employees assigned yet.</div>
                @endforelse
            </div>
            <div class="mt-4">{{ $employees->links() }}</div>
        </section>

        <aside class="space-y-4">
            <div class="soft-card p-5">
                <h2 class="text-lg font-black text-slate-950">Quick Actions</h2>
                <div class="mt-4 grid gap-2">
                    <a class="quick-tile" href="{{ route('attendance.take', ['project_id' => $project->id]) }}"><span>✓</span> Take Attendance</a>
                    <a class="quick-tile" href="{{ route('attendance.index', ['project_id' => $project->id, 'month' => now()->month, 'year' => now()->year]) }}"><span>▦</span> Monthly Attendance</a>
                    <a class="quick-tile" href="{{ route('absent.index', ['project_id' => $project->id, 'month' => now()->month, 'year' => now()->year]) }}"><span>!</span> Absent Report</a>
                    <a class="quick-tile" href="{{ route('reports.index', ['project_id' => $project->id]) }}"><span>↓</span> Generate Reports</a>
                </div>
            </div>

            <div class="soft-card p-5">
                <h2 class="text-lg font-black text-slate-950">Assign Employees</h2>
                <p class="mt-1 text-sm text-slate-500">Tick employees and assign them to this project.</p>
                <form class="mt-4 grid gap-3" method="post" action="{{ route('projects.assign-employees', $project) }}">
                    @csrf
                    <div class="max-h-96 overflow-y-auto rounded-2xl border p-2">
                        @forelse($availableEmployees as $employee)
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl p-2 hover:bg-slate-50">
                                <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" class="mt-1 h-4 w-4">
                                <span>
                                    <span class="block text-sm font-black">{{ $employee->name }}</span>
                                    <span class="block text-xs text-slate-500">{{ $employee->iqama_no ?: 'No Iqama' }} · Current: {{ optional($employee->currentProject)->name ?: 'None' }}</span>
                                </span>
                            </label>
                        @empty
                            <div class="p-3 text-sm text-slate-500">No available employees found.</div>
                        @endforelse
                    </div>
                    <button class="btn-primary rounded-2xl">Assign Selected</button>
                </form>
            </div>
        </aside>
    </div>
</div>

<script>
function filterProjectEmployees() {
    const query = (document.getElementById('projectEmployeeSearch').value || '').toLowerCase().trim();
    document.querySelectorAll('.employee-mini').forEach(card => {
        card.style.display = !query || card.dataset.search.includes(query) ? '' : 'none';
    });
}
</script>
@endsection
