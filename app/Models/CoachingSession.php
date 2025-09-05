<?php

namespace App\Models;


use App\Models\Traits\CompanyScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// app/Models/CoachingSession.php
class CoachingSession extends Model {
    use HasFactory, CompanyScoped;
    protected $fillable=['company_id','employee_id','date','observations','recommendations','follow_up_date','attachments','created_by'];
    protected $casts=['observations'=>'array','recommendations'=>'array','attachments'=>'array','date'=>'date','follow_up_date'=>'date'];
    public function employee(){ return $this->belongsTo(Employee::class); }
}
