<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Companies / Subcontractors
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('type')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Projects
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('active');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Designations / Trades
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Employees
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->nullable()->unique();
            $table->string('name')->index();
            $table->string('normalized_name')->index();
            $table->string('iqama_no')->nullable()->unique();
            $table->string('passport_no')->nullable()->index();
            $table->foreignId('designation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('current_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('shift')->nullable();
            $table->string('accommodation')->nullable();
            $table->date('doj')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('hourly_rate', 12, 2)->nullable();
            $table->decimal('monthly_salary', 12, 2)->nullable();
            $table->string('status')->default('active');
            $table->text('remarks')->nullable();
            $table->string('source_sheet')->nullable();
            $table->timestamps();
        });

        // Employee Project Assignment History
        Schema::create('employee_project_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Attendance Months Tracking
        Schema::create('attendance_months', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->string('source_file')->nullable();
            $table->string('source_sheet')->nullable();
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
        });

        // Daily Attendance Records
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->date('attendance_date')->index();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->string('status')->default('empty')->index();
            $table->decimal('hours', 8, 2)->nullable();
            $table->string('raw_value')->nullable();
            $table->boolean('is_extra_duty')->default(false);
            $table->string('extra_duty_type')->nullable();
            $table->decimal('extra_duty_hours', 8, 2)->nullable();
            $table->decimal('extra_duty_amount', 12, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->string('source_sheet')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'attendance_date', 'project_id'], 'att_unique');
        });

        // Advance Payments
        Schema::create('advance_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date')->nullable();
            $table->string('payment_type')->default('manual');
            $table->string('source_sheet')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Extra Duty / Friday Duty / Overtime
        Schema::create('extra_duties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->date('duty_date');
            $table->string('day_type')->default('normal_day'); // friday, public_holiday, off_day, normal_day, overtime
            $table->decimal('hours_worked', 8, 2);
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('approval_status')->default('pending'); // pending, approved, rejected
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->string('source_sheet')->nullable();
            $table->timestamps();
        });

        // Payslips
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('total_hours', 10, 2)->default(0);
            $table->decimal('normal_hours', 10, 2)->default(0);
            $table->decimal('extra_duty_hours', 10, 2)->default(0);
            $table->unsignedSmallInteger('days_worked')->default(0);
            $table->unsignedSmallInteger('absent_days')->default(0);
            $table->decimal('hourly_rate', 12, 2)->nullable();
            $table->decimal('monthly_salary', 12, 2)->nullable();
            $table->decimal('normal_salary', 12, 2)->default(0);
            $table->decimal('extra_duty_amount', 12, 2)->default(0);
            $table->decimal('gross_salary', 12, 2)->default(0);
            $table->decimal('advance_deduction', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('generated_at')->nullable();
            $table->string('status')->default('draft'); // draft, final
            $table->timestamps();
        });

        // Generated Reports
        Schema::create('generated_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type');
            $table->string('report_name');
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('month')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->json('filters')->nullable();
            $table->string('file_type');
            $table->string('file_path');
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('generated_at')->nullable();
            $table->string('status')->default('completed');
            $table->timestamps();
        });

        // Import Logs
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->string('import_type');
            $table->string('file_name');
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('month')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('success_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);
            $table->string('status')->default('completed');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $tables = [
            'imports',
            'generated_reports',
            'payslips',
            'extra_duties',
            'advance_payments',
            'attendance_records',
            'attendance_months',
            'employee_project_assignments',
            'employees',
            'designations',
            'projects',
            'companies',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
