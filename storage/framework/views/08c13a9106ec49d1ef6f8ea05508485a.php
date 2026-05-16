
<?php $__env->startSection('title','Projects'); ?>
<?php $__env->startSection('content'); ?>
<style>
    .project-hero{background:linear-gradient(135deg,#0f766e,#0891b2);border-radius:28px;color:#fff;box-shadow:0 20px 42px rgba(8,145,178,.18);}
    .project-card{border:1px solid #e2e8f0;border-radius:26px;background:#fff;box-shadow:0 14px 32px rgba(15,23,42,.06);transition:.18s ease;overflow:hidden;}
    .project-card:hover{transform:translateY(-2px);box-shadow:0 22px 46px rgba(15,23,42,.1);border-color:#99f6e4;}
    .project-action{border-radius:16px;border:1px solid #dbe3ef;background:#fff;padding:.75rem .85rem;text-align:center;font-weight:900;font-size:.82rem;color:#0f172a;transition:.16s ease;}
    .project-action:hover{background:#f0fdfa;border-color:#5eead4;color:#0f766e;}
    .project-primary{background:#0f766e;color:#fff;border-color:#0f766e;}
    .project-primary:hover{background:#115e59;color:#fff;}
</style>

<div class="space-y-5">
    <section class="project-hero p-5 md:p-7">
        <div class="grid gap-5 lg:grid-cols-[1fr_360px] lg:items-end">
            <div>
                <div class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black ring-1 ring-white/25">Project Dashboard</div>
                <h2 class="mt-3 text-2xl font-black md:text-4xl">Manage projects without confusion.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-cyan-50 md:text-base">Open any project to take attendance, check monthly sheets, view absent report, assign employees, and track project-wise work.</p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <a class="rounded-2xl bg-white px-4 py-3 text-sm font-black text-teal-700 shadow" href="<?php echo e(route('projects.create')); ?>">+ Add Project</a>
                    <a class="rounded-2xl bg-amber-400 px-4 py-3 text-sm font-black text-amber-950 shadow" href="<?php echo e(route('attendance.take')); ?>">Take Attendance</a>
                </div>
            </div>
            <form class="rounded-3xl bg-white/95 p-4 text-slate-900 shadow-xl">
                <label class="text-xs font-black uppercase tracking-wide text-slate-500">Find project</label>
                <input class="input mt-1 w-full rounded-2xl" name="search" value="<?php echo e($search); ?>" placeholder="Search project name, code, location">
                <div class="mt-3 grid grid-cols-[1fr_auto] gap-2">
                    <select class="input rounded-2xl" name="status">
                        <option value="">All Status</option>
                        <option value="active" <?php if($status === 'active'): echo 'selected'; endif; ?>>Active</option>
                        <option value="inactive" <?php if($status === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
                    </select>
                    <button class="btn-primary rounded-2xl">Filter</button>
                </div>
            </form>
        </div>
    </section>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="project-card">
                <div class="p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="truncate text-xl font-black text-slate-950"><?php echo e($project->name); ?></h2>
                            <p class="mt-1 text-sm text-slate-500"><?php echo e($project->code ?: 'No code'); ?> · <?php echo e($project->location ?: 'No location'); ?></p>
                        </div>
                        <span class="badge <?php echo e($project->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'); ?>"><?php echo e(ucfirst($project->status)); ?></span>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-xs font-black uppercase text-slate-400">Employees</div>
                            <div class="mt-1 text-3xl font-black text-slate-950"><?php echo e($project->employees_count); ?></div>
                        </div>
                        <div class="rounded-2xl bg-teal-50 p-4">
                            <div class="text-xs font-black uppercase text-teal-600">Quick Status</div>
                            <div class="mt-1 text-lg font-black text-teal-800"><?php echo e($project->status === 'active' ? 'Ready' : 'Inactive'); ?></div>
                        </div>
                    </div>

                    <?php if($project->remarks): ?>
                        <p class="mt-4 line-clamp-2 rounded-2xl bg-slate-50 p-3 text-sm text-slate-600"><?php echo e($project->remarks); ?></p>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-2 gap-2 border-t bg-slate-50 p-3">
                    <a class="project-action project-primary" href="<?php echo e(route('projects.show', $project)); ?>">Open Project</a>
                    <a class="project-action" href="<?php echo e(route('attendance.take', ['project_id' => $project->id])); ?>">Take Attendance</a>
                    <a class="project-action" href="<?php echo e(route('attendance.index', ['project_id' => $project->id, 'month' => now()->month, 'year' => now()->year])); ?>">Monthly Sheet</a>
                    <a class="project-action" href="<?php echo e(route('projects.edit', $project)); ?>">Edit</a>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="card md:col-span-2 xl:col-span-3 text-center text-slate-500">No projects found.</div>
        <?php endif; ?>
    </div>

    <div><?php echo e($projects->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\employee-management\resources\views/projects/index.blade.php ENDPATH**/ ?>