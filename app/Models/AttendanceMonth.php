<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Factories\HasFactory;
class AttendanceMonth extends Model{use HasFactory;protected $fillable=['project_id', 'month', 'year', 'source_file', 'source_sheet', 'imported_by', 'imported_at'];protected $casts=['imported_at'=>'datetime'];}
