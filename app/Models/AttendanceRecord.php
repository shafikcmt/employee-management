<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Factories\HasFactory;
class AttendanceRecord extends Model{use HasFactory;protected $fillable=['employee_id', 'project_id', 'attendance_date', 'month', 'year', 'status', 'hours', 'raw_value', 'is_extra_duty', 'extra_duty_type', 'extra_duty_hours', 'extra_duty_amount', 'remarks', 'source_sheet'];protected $casts=['attendance_date'=>'date','is_extra_duty'=>'boolean'];public function employee(){return $this->belongsTo(Employee::class);}public function project(){return $this->belongsTo(Project::class);}}
