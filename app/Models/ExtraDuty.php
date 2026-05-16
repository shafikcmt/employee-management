<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Factories\HasFactory;
class ExtraDuty extends Model{use HasFactory;protected $fillable=['employee_id', 'project_id', 'duty_date', 'day_type', 'hours_worked', 'rate', 'amount', 'approval_status', 'approved_by', 'approved_at', 'remarks', 'source_sheet'];protected $casts=['duty_date'=>'date','approved_at'=>'datetime'];public function employee(){return $this->belongsTo(Employee::class);}public function project(){return $this->belongsTo(Project::class);}}
