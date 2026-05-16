<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Factories\HasFactory;
class AdvancePayment extends Model{use HasFactory;protected $fillable=['employee_id', 'project_id', 'amount', 'payment_date', 'payment_type', 'source_sheet', 'remarks'];protected $casts=['payment_date'=>'date'];public function employee(){return $this->belongsTo(Employee::class);}public function project(){return $this->belongsTo(Project::class);}}
