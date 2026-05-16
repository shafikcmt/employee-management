<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Factories\HasFactory;
class ImportLog extends Model{use HasFactory;protected $fillable=['import_type', 'file_name', 'project_id', 'month', 'year', 'imported_by', 'total_rows', 'success_rows', 'failed_rows', 'status', 'remarks'];protected $casts=[];}
