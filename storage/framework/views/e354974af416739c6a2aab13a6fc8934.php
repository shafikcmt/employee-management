<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(config('app.name', 'Employee Management')); ?> - <?php echo $__env->yieldContent('title'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        :root {
            --teal: #0f766e;
            --teal-dark: #0d5a55;
            --slate-900: #0f172a;
        }
        
        .sidebar-nav a {
            @apply block rounded-lg px-4 py-2.5 text-sm font-semibold transition-all;
        }
        
        .sidebar-nav a:not(.router-link-active) {
            @apply text-slate-600 hover:bg-slate-100;
        }
        
        .sidebar-nav a.active {
            @apply bg-teal-100 text-teal-700;
        }
        
        .page-title {
            @apply text-2xl md:text-3xl font-black text-slate-900;
        }
        
        .toast {
            @apply rounded-lg px-4 py-3 shadow-sm animate-slide-up;
        }
        
        .toast-success {
            @apply bg-emerald-50 text-emerald-800 border border-emerald-200;
        }
        
        .toast-error {
            @apply bg-red-50 text-red-800 border border-red-200;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

<div class="min-h-screen flex flex-col md:flex-row">
    <!-- Sidebar -->
    <aside class="w-full md:w-72 bg-white border-r border-slate-200 flex-shrink-0">
        <!-- Logo -->
        <div class="p-6 border-b border-slate-200">
            <div class="rounded-xl bg-gradient-to-br from-teal-600 to-teal-700 px-4 py-4 text-white">
                <div class="text-lg font-black">Employee MS</div>
                <div class="text-xs text-teal-100 mt-1">Attendance • Payroll • Reports</div>
            </div>
        </div>

        <!-- Quick Action Button -->
        <div class="p-4 border-b border-slate-200">
            <a href="<?php echo e(route('attendance.take')); ?>" 
               class="w-full block text-center bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-bold py-3 rounded-xl transition-all shadow-md hover:shadow-lg">
                📋 Take Attendance
            </a>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-1 sidebar-nav overflow-y-auto max-h-[calc(100vh-240px)]">
            <?php
                $menu = [
                    ['Dashboard', 'dashboard', 'dashboard'],
                    ['Employees', 'employees.index', 'people'],
                    ['Attendance Sheet', 'attendance.index', 'calendar'],
                    ['Absent Report', 'absent.index', 'alert'],
                    ['Projects', 'projects.index', 'briefcase'],
                    ['Advances', 'advance-payments.index', 'dollar'],
                    ['Extra Duty', 'extra-duties.index', 'clock'],
                    ['Payslips', 'payslips.index', 'receipt'],
                    ['Reports', 'reports.index', 'chart'],
                    ['Imports', 'imports.index', 'upload'],
                    ['Refs', '#', 'settings', ['Companies', 'companies.index', 'Designations', 'designations.index']],
                ];
            ?>

            <?php $__currentLoopData = $menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(count($item) === 3): ?>
                    <a href="<?php echo e(route($item[1])); ?>" 
                       class="<?php echo e(request()->routeIs($item[1]) ? 'active' : ''); ?>">
                        <?php echo e($item[0]); ?>

                    </a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>

        <!-- User Section -->
        <div class="absolute bottom-0 left-0 w-full md:w-72 p-4 bg-slate-50 border-t border-slate-200">
            <form method="post" action="<?php echo e(route('logout')); ?>" class="w-full">
                <?php echo csrf_field(); ?>
                <button type="submit" 
                        class="w-full inline-flex items-center justify-between px-4 py-2.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 font-semibold text-sm transition-all">
                    <span><?php echo e(auth()->user()->name ?? 'User'); ?></span>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 pb-20 md:pb-0">
        <!-- Top Bar -->
        <div class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-sm">
            <div class="px-6 py-4 flex items-center justify-between">
                <h1 class="page-title"><?php echo $__env->yieldContent('title'); ?></h1>
                <div class="text-sm text-slate-600">
                    <?php echo e(now()->format('l, d M Y')); ?>

                </div>
            </div>
        </div>

        <!-- Alerts -->
        <div class="px-6 pt-4">
            <?php if(session('success')): ?>
                <div class="toast toast-success mb-4">
                    <strong>✓ Success!</strong> <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="toast toast-error mb-4">
                    <strong>✗ Error!</strong> <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="toast toast-error mb-4">
                    <strong>✗ Error!</strong> <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>
        </div>

        <!-- Page Content -->
        <div class="px-6 py-6">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>
</div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\employee-management\resources\views/layouts/app.blade.php ENDPATH**/ ?>