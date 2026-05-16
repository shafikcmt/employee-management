<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'location', 'status', 'remarks'];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'current_project_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function advancePayments()
    {
        return $this->hasMany(AdvancePayment::class);
    }

    public function extraDuties()
    {
        return $this->hasMany(ExtraDuty::class);
    }

    public function payslips()
    {
        return $this->hasMany(Payslip::class);
    }
}
