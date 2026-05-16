<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Factories\HasFactory;
class EmployeeProjectAssignment extends Model{use HasFactory;protected $fillable=['employee_id', 'project_id', 'start_date', 'end_date', 'status', 'remarks'];protected $casts=['start_date'=>'date','end_date'=>'date'];}
