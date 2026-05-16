@extends('layouts.app')
@section('title',$project->exists ? 'Edit Project' : 'Add Project')
@section('content')
<form class="card max-w-3xl" method="post" action="{{ $project->exists ? route('projects.update', $project) : route('projects.store') }}">
    @csrf
    @if($project->exists)
        @method('put')
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
            <label class="text-sm font-semibold">Project Name</label>
            <input class="input mt-1 w-full" name="name" value="{{ old('name', $project->name) }}" required placeholder="Example: WADI SAFAR">
        </div>
        <div>
            <label class="text-sm font-semibold">Project Code</label>
            <input class="input mt-1 w-full" name="code" value="{{ old('code', $project->code) }}" placeholder="Optional code">
        </div>
        <div>
            <label class="text-sm font-semibold">Location</label>
            <input class="input mt-1 w-full" name="location" value="{{ old('location', $project->location) }}" placeholder="Optional location">
        </div>
        <div>
            <label class="text-sm font-semibold">Status</label>
            <select class="input mt-1 w-full" name="status" required>
                <option value="active" @selected(old('status', $project->status ?: 'active') === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $project->status) === 'inactive')>Inactive</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="text-sm font-semibold">Remarks</label>
            <textarea class="input mt-1 w-full" name="remarks" rows="4" placeholder="Project notes">{{ old('remarks', $project->remarks) }}</textarea>
        </div>
    </div>

    <div class="mt-5 flex flex-wrap gap-2">
        <button class="btn-primary">Save Project</button>
        <a class="btn-light" href="{{ route('projects.index') }}">Cancel</a>
    </div>
</form>
@endsection
