<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo e(config('app.name')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css','resources/js/app.js']); ?>
</head>
<body class="bg-slate-100 text-slate-900">
<div class="min-h-screen md:flex">
    <aside class="bg-white md:w-72 p-4 border-r">
        <div class="rounded-2xl bg-teal-700 px-4 py-3 text-white">
            <b class="text-lg">Employee MS</b>
            <div class="text-xs text-teal-100">Attendance, salary and reports</div>
        </div>

        <a class="mt-4 block rounded-xl bg-amber-500 px-4 py-3 text-center text-sm font-bold text-white hover:bg-amber-600" href="<?php echo e(route('attendance.take')); ?>">
            Take Attendance
        </a>

        <nav class="mt-5 grid gap-1">
            <?php $__currentLoopData = [
                ['Dashboard','dashboard'],
                ['Employees','employees.index'],
                ['Attendance Sheet','attendance.index'],
                ['Take Attendance','attendance.take'],
                ['Absent Report','absent.index'],
                ['Advances','advance-payments.index'],
                ['Extra Duty','extra-duties.index'],
                ['Payslips','payslips.index'],
                ['Reports','reports.index'],
                ['Imports','imports.index'],
                ['Projects','projects.index'],
                ['Companies','companies.index'],
                ['Designations','designations.index'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label,$route]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a class="rounded-xl px-3 py-2 text-sm hover:bg-slate-50 <?php echo e(request()->routeIs($route) ? 'bg-slate-100 font-semibold text-teal-700' : ''); ?>" href="<?php echo e(route($route)); ?>">
                    <?php echo e($label); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>
    </aside>

    <main class="flex-1 p-4 md:p-6">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-bold"><?php echo $__env->yieldContent('title'); ?></h1>
            <form method="post" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button class="btn-light">Logout</button>
            </form>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-4 rounded-xl bg-emerald-50 p-3 text-emerald-700"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mb-4 rounded-xl bg-red-50 p-3 text-red-700"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\employee-management\resources\views/layouts/app.blade.php ENDPATH**/ ?>