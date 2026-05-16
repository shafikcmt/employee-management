
<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startSection('content'); ?>

<style>
    .stat-card {
        @apply bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow;
    }
    
    .stat-number {
        @apply text-4xl font-black text-slate-900;
    }
    
    .stat-label {
        @apply text-sm font-bold text-slate-500 uppercase tracking-wide mt-2;
    }
    
    .stat-badge {
        @apply inline-block px-3 py-1 rounded-lg text-xs font-bold mt-3;
    }
    
    .badge-teal {
        @apply bg-teal-100 text-teal-800;
    }
    
    .badge-emerald {
        @apply bg-emerald-100 text-emerald-800;
    }
    
    .badge-red {
        @apply bg-red-100 text-red-800;
    }
    
    .badge-amber {
        @apply bg-amber-100 text-amber-800;
    }
    
    .badge-blue {
        @apply bg-blue-100 text-blue-800;
    }
    
    .action-btn {
        @apply inline-flex items-center gap-2 px-4 py-3 rounded-xl font-bold text-sm transition-all;
    }
    
    .action-btn-primary {
        @apply bg-teal-600 text-white hover:bg-teal-700 shadow-md hover:shadow-lg;
    }
    
    .action-btn-secondary {
        @apply bg-slate-100 text-slate-900 hover:bg-slate-200;
    }
    
    .quick-action-card {
        @apply bg-white rounded-xl p-4 border border-slate-100 hover:border-teal-300 hover:shadow-md transition-all cursor-pointer;
    }
</style>

<!-- Quick Stats Row -->
<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 mb-6">
    <!-- Employees Stats -->
    <div class="stat-card">
        <div class="stat-label">Total Employees</div>
        <div class="stat-number"><?php echo e($totalEmployees); ?></div>
        <div class="mt-2 text-sm text-slate-600">
            <span class="stat-badge badge-emerald"><?php echo e($activeEmployees); ?> Active</span>
            <?php if($inactiveEmployees > 0): ?>
                <span class="stat-badge badge-slate-200"><?php echo e($inactiveEmployees); ?> Inactive</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Today's Attendance -->
    <div class="stat-card">
        <div class="stat-label">Today's Attendance</div>
        <div class="text-2xl font-black text-slate-900">
            <span class="text-emerald-600"><?php echo e($presentToday); ?></span>
            <span class="text-slate-300 text-lg">/</span>
            <span class="text-slate-500"><?php echo e($presentToday + $absentToday + $fridayToday); ?></span>
        </div>
        <div class="mt-2 text-xs text-slate-600 space-y-1">
            <div><span class="font-bold text-emerald-600">✓</span> Present: <?php echo e($presentToday); ?></div>
            <div><span class="font-bold text-red-600">✗</span> Absent: <?php echo e($absentToday); ?></div>
            <?php if($fridayToday > 0): ?>
                <div><span class="font-bold text-amber-600">📅</span> Friday: <?php echo e($fridayToday); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Monthly Hours -->
    <div class="stat-card">
        <div class="stat-label">Monthly Hours</div>
        <div class="stat-number"><?php echo e(number_format($monthlyTotalHours, 1)); ?></div>
        <div class="mt-2 text-sm text-slate-600">
            <span class="stat-badge badge-blue">+<?php echo e(number_format($monthlyExtraDutyHours, 1)); ?> Extra</span>
        </div>
    </div>

    <!-- Payroll Summary -->
    <div class="stat-card">
        <div class="stat-label">Payroll (This Month)</div>
        <div class="stat-number text-2xl">
            <?php echo e(number_format($monthlyNetSalary, 0)); ?>

            <span class="text-xs font-bold text-slate-400">SAR</span>
        </div>
        <div class="mt-2 text-xs text-slate-600 space-y-1">
            <div><span class="font-bold text-slate-700">Gross:</span> <?php echo e(number_format($monthlyGrossSalary, 0)); ?> SAR</div>
            <div><span class="font-bold text-slate-700">Adv:</span> <?php echo e(number_format($monthlyAdvancePaid, 0)); ?> SAR</div>
        </div>
    </div>
</div>

<!-- Financial Stats -->
<div class="grid gap-4 md:grid-cols-3 mb-6">
    <!-- Advance Payments -->
    <div class="stat-card">
        <div class="stat-label">Advance Payments (Total)</div>
        <div class="stat-number text-3xl"><?php echo e(number_format($totalAdvancePaid, 0)); ?></div>
        <div class="text-xs text-slate-500 mt-2">SAR</div>
        <div class="mt-2 text-sm text-slate-600">
            <span class="stat-badge badge-teal">This Month: <?php echo e(number_format($monthlyAdvancePaid, 0)); ?> SAR</span>
        </div>
    </div>

    <!-- Extra Duty Amount -->
    <div class="stat-card">
        <div class="stat-label">Extra Duty Amount (Approved)</div>
        <div class="stat-number text-3xl"><?php echo e(number_format($monthlyApprovedExtraDutyAmount, 0)); ?></div>
        <div class="text-xs text-slate-500 mt-2">SAR (This Month)</div>
        <div class="mt-2 text-xs text-slate-600 space-y-1">
            <div><span class="font-bold">Pending:</span> <?php echo e(number_format($pendingExtraDutyAmount, 0)); ?> SAR</div>
        </div>
    </div>

    <!-- Payslip Status -->
    <div class="stat-card">
        <div class="stat-label">Payslips (This Month)</div>
        <div class="text-2xl font-black text-slate-900">
            <span class="text-blue-600"><?php echo e($finalPayslips); ?></span>
            <span class="text-slate-300">/</span>
            <span class="text-slate-500"><?php echo e($finalPayslips + $draftPayslips); ?></span>
        </div>
        <div class="mt-2 text-xs text-slate-600 space-y-1">
            <div><span class="font-bold text-emerald-600">✓</span> Final: <?php echo e($finalPayslips); ?></div>
            <div><span class="font-bold text-amber-600">○</span> Draft: <?php echo e($draftPayslips); ?></div>
        </div>
    </div>
</div>

<!-- Extra Duty & Projects -->
<div class="grid gap-4 md:grid-cols-3 mb-6">
    <!-- Extra Duty Status -->
    <div class="stat-card">
        <div class="stat-label">Extra Duty Approvals</div>
        <div class="text-2xl font-black text-slate-900">
            <?php echo e($pendingExtraDuties); ?> <span class="text-sm text-slate-400">pending</span>
        </div>
        <div class="mt-3 space-y-1 text-xs text-slate-600">
            <div class="flex justify-between">
                <span><span class="font-bold text-emerald-600">✓</span> Approved:</span>
                <span class="font-bold"><?php echo e($approvedExtraDuties); ?></span>
            </div>
            <div class="flex justify-between">
                <span><span class="font-bold text-red-600">✗</span> Rejected:</span>
                <span class="font-bold"><?php echo e($rejectedExtraDuties); ?></span>
            </div>
        </div>
    </div>

    <!-- Projects -->
    <div class="stat-card">
        <div class="stat-label">Active Projects</div>
        <div class="stat-number text-3xl"><?php echo e($totalProjects); ?></div>
        <div class="text-xs text-slate-500 mt-2">Projects</div>
        <a href="<?php echo e(route('projects.index')); ?>" class="inline-block mt-2 text-xs font-bold text-teal-600 hover:text-teal-700">
            View All →
        </a>
    </div>

    <!-- Reports -->
    <div class="stat-card">
        <div class="stat-label">Report History</div>
        <div class="stat-number text-3xl"><?php echo e($totalReportsGenerated); ?></div>
        <div class="text-xs text-slate-500 mt-2">Reports generated</div>
        <a href="<?php echo e(route('reports.index')); ?>" class="inline-block mt-2 text-xs font-bold text-teal-600 hover:text-teal-700">
            View Reports →
        </a>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-2xl p-6 border border-slate-100 mb-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">Quick Actions</h2>
    <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-4">
        <a href="<?php echo e(route('attendance.take')); ?>" class="quick-action-card">
            <div class="text-lg font-bold text-slate-900">📋</div>
            <div class="font-bold text-slate-900 mt-2">Take Attendance</div>
            <div class="text-xs text-slate-500 mt-1">Mark present/absent</div>
        </a>

        <a href="<?php echo e(route('attendance.index')); ?>" class="quick-action-card">
            <div class="text-lg font-bold text-slate-900">📊</div>
            <div class="font-bold text-slate-900 mt-2">Monthly Sheet</div>
            <div class="text-xs text-slate-500 mt-1">View full month</div>
        </a>

        <a href="<?php echo e(route('payslips.index')); ?>" class="quick-action-card">
            <div class="text-lg font-bold text-slate-900">💰</div>
            <div class="font-bold text-slate-900 mt-2">Generate Payslips</div>
            <div class="text-xs text-slate-500 mt-1">Create salary slips</div>
        </a>

        <a href="<?php echo e(route('reports.index')); ?>" class="quick-action-card">
            <div class="text-lg font-bold text-slate-900">📁</div>
            <div class="font-bold text-slate-900 mt-2">Generate Reports</div>
            <div class="text-xs text-slate-500 mt-1">All reports in ZIP</div>
        </a>

        <a href="<?php echo e(route('advance-payments.index')); ?>" class="quick-action-card">
            <div class="text-lg font-bold text-slate-900">💵</div>
            <div class="font-bold text-slate-900 mt-2">Add Advance</div>
            <div class="text-xs text-slate-500 mt-1">New advance payment</div>
        </a>

        <a href="<?php echo e(route('extra-duties.index')); ?>" class="quick-action-card">
            <div class="text-lg font-bold text-slate-900">⏱️</div>
            <div class="font-bold text-slate-900 mt-2">Extra Duties</div>
            <div class="text-xs text-slate-500 mt-1">Approve/Manage</div>
        </a>

        <a href="<?php echo e(route('absent.index')); ?>" class="quick-action-card">
            <div class="text-lg font-bold text-slate-900">❌</div>
            <div class="font-bold text-slate-900 mt-2">Absent Report</div>
            <div class="text-xs text-slate-500 mt-1">View absences</div>
        </a>

        <a href="<?php echo e(route('imports.index')); ?>" class="quick-action-card">
            <div class="text-lg font-bold text-slate-900">📥</div>
            <div class="font-bold text-slate-900 mt-2">Import Excel</div>
            <div class="text-xs text-slate-500 mt-1">Bulk upload data</div>
        </a>
    </div>
</div>

<!-- Recent Reports -->
<?php if($recentReports->count() > 0): ?>
    <div class="bg-white rounded-2xl p-6 border border-slate-100">
        <h2 class="text-xl font-bold text-slate-900 mb-4">Recent Reports</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-3 px-4 font-bold text-slate-600">Report</th>
                        <th class="text-left py-3 px-4 font-bold text-slate-600">Type</th>
                        <th class="text-left py-3 px-4 font-bold text-slate-600">Generated</th>
                        <th class="text-left py-3 px-4 font-bold text-slate-600">By</th>
                        <th class="text-center py-3 px-4 font-bold text-slate-600">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $recentReports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900"><?php echo e($report->report_name); ?></div>
                                <div class="text-xs text-slate-500 mt-1">
                                    <?php if($report->month): ?>
                                        <?php echo e(date('F', mktime(0, 0, 0, $report->month, 1))); ?> <?php echo e($report->year); ?>

                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2 py-1 rounded bg-slate-100 text-xs font-bold text-slate-700">
                                    <?php echo e(strtoupper($report->file_type)); ?>

                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-600">
                                <?php echo e($report->generated_at?->diffForHumans()); ?>

                            </td>
                            <td class="py-3 px-4 text-xs text-slate-600">
                                <?php echo e($report->generatedBy?->name ?? 'System'); ?>

                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="<?php echo e(route('reports.download', $report)); ?>" 
                                   class="inline-block px-3 py-1 rounded bg-teal-100 text-teal-700 hover:bg-teal-200 text-xs font-bold">
                                    Download
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\employee-management\resources\views/dashboard.blade.php ENDPATH**/ ?>