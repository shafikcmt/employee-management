<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GeneratedReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_type',
        'report_name',
        'project_id',
        'employee_id',
        'month',
        'year',
        'filters',
        'file_type',
        'file_path',
        'generated_by',
        'generated_at',
        'status',
    ];

    protected $casts = [
        'filters' => 'array',
        'generated_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
