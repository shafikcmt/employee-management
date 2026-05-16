
<?php $__env->startSection('title','Monthly Attendance Sheet'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-4 grid gap-3 md:grid-cols-2">
    <div class="card">
        <h2 class="text-lg font-bold">Attendance Controls</h2>
        <p class="mt-1 text-sm text-slate-500">Use this page to review monthly attendance. Click Take Attendance to enter daily attendance for all employees.</p>
        <div class="mt-4 flex flex-wrap gap-2">
            <a class="btn-primary" href="<?php echo e(route('attendance.take', ['project_id' => $pid, 'date' => now()->toDateString()])); ?>">Take Today Attendance</a>
            <a class="btn-light" href="<?php echo e(route('absent.index', ['project_id' => $pid, 'month' => $month, 'year' => $year])); ?>">View Absent Report</a>
        </div>
    </div>
    <form class="card grid gap-3 md:grid-cols-4">
        <select name="project_id" class="input md:col-span-2">
            <option value="">All Projects</option>
            <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($project->id); ?>" <?php if($pid == $project->id): echo 'selected'; endif; ?>><?php echo e($project->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <input class="input" name="month" type="number" min="1" max="12" value="<?php echo e($month); ?>" placeholder="Month">
        <input class="input" name="year" type="number" value="<?php echo e($year); ?>" placeholder="Year">
        <button class="btn-primary md:col-span-4">Load Monthly Sheet</button>
    </form>
</div>

<div class="card overflow-x-auto">
    <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="font-bold"><?php echo e(date('F', mktime(0,0,0,$month,1))); ?> <?php echo e($year); ?></h2>
            <p class="text-sm text-slate-500">Legend: numeric = hours, A = absent, Fri = Friday, PH = public holiday, NW = not working.</p>
        </div>
        <div class="flex flex-wrap gap-2 text-xs">
            <span class="badge bg-emerald-100 text-emerald-700">Present</span>
            <span class="badge bg-red-100 text-red-700">Absent</span>
            <span class="badge bg-amber-100 text-amber-700">Extra Duty</span>
            <span class="badge bg-slate-100 text-slate-700">Off / Empty</span>
        </div>
    </div>

    <table class="w-full min-w-[1100px] text-xs">
        <thead>
            <tr>
                <th class="table-th sticky left-0 bg-slate-50">Employee</th>
                <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th class="table-th text-center"><?php echo e($day); ?></th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <th class="table-th text-right">Hours</th>
                <th class="table-th text-right">Extra</th>
                <th class="table-th text-right">Absent</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php ($totalHours = 0); ?>
                <?php ($extraHours = 0); ?>
                <?php ($absentDays = 0); ?>
                <tr>
                    <td class="table-td sticky left-0 bg-white font-bold">
                        <div><?php echo e($employee->name); ?></div>
                        <div class="text-xs font-normal text-slate-500"><?php echo e($employee->iqama_no ?: 'No Iqama'); ?> · <?php echo e(optional($employee->designation)->name); ?></div>
                    </td>
                    <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php ($record = optional(($records[$employee->id.'_'.$day] ?? collect())->first())); ?>
                        <?php ($totalHours += (float)($record->hours ?? 0)); ?>
                        <?php ($extraHours += (float)($record->extra_duty_hours ?? 0)); ?>
                        <?php ($absentDays += $record->status === 'absent' ? 1 : 0); ?>
                        <?php ($cellClass = $record->is_extra_duty ? 'bg-amber-50 text-amber-700 font-bold' : ($record->status === 'absent' ? 'bg-red-50 text-red-700 font-bold' : ($record->status === 'present' ? 'bg-emerald-50 text-emerald-700' : ''))); ?>
                        <td class="table-td text-center <?php echo e($cellClass); ?>"><?php echo e($record->raw_value ?: '-'); ?></td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <td class="table-td text-right font-bold"><?php echo e(number_format($totalHours, 2)); ?></td>
                    <td class="table-td text-right font-bold text-amber-700"><?php echo e(number_format($extraHours, 2)); ?></td>
                    <td class="table-td text-right font-bold text-red-700"><?php echo e($absentDays); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td class="table-td text-center text-slate-500" colspan="<?php echo e(count($days) + 4); ?>">No employees found for this filter.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\employee-management\resources\views/attendance/index.blade.php ENDPATH**/ ?>