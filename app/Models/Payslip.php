<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Factories\HasFactory;
class Payslip extends Model{use HasFactory;protected $fillable=['employee_id', 'project_id', 'month', 'year', 'total_hours', 'normal_hours', 'extra_duty_hours', 'days_worked', 'absent_days', 'hourly_rate', 'monthly_salary', 'normal_salary', 'extra_duty_amount', 'gross_salary', 'advance_deduction', 'net_salary', 'generated_by', 'generated_at', 'status'];protected $casts=['generated_at'=>'datetime'];public function employee(){return $this->belongsTo(Employee::class);}public function project(){return $this->belongsTo(Project::class);}}
