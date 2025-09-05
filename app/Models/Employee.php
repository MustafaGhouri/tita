<?php


// app/Models/Employee.php
namespace App\Models;
use App\Models\Traits\CompanyScoped;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Employee extends Model {
    use HasFactory, SoftDeletes, CompanyScoped;
    protected $fillable=['company_id','first_name','last_name','email','position','status'];
    protected $appends=['name'];

    public function getNameAttribute(){ 
    	return trim($this->first_name.' '.$this->last_name); 
        }
    public function evaluations(){ 
    	return $this->hasMany(MysteryEvaluation::class); 
    }
    public function diagnosticResult(){ 
    	return $this->hasOne(DiagnosticResult::class);
    	 }

      // 👇 yeh relation REQUIRED hai agar aap factory->for($company) use karte ho
    public function company()
    {
        return $this->belongsTo(Company::class);
    }


}
