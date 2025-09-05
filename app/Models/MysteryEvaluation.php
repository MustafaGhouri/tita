<?php

namespace App\Models;

use App\Models\Traits\CompanyScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// app/Models/MysteryEvaluation.php
class MysteryEvaluation extends Model {
    use HasFactory, CompanyScoped;
    protected $fillable=['company_id','employee_id','checklist_id','monthKey','answers','score','video_path','created_by'];
    protected $casts=['answers'=>'array'];
    public function employee(){ return $this->belongsTo(Employee::class); }
    public function checklist(){ return $this->belongsTo(MysteryChecklist::class, 'checklist_id'); }
}
