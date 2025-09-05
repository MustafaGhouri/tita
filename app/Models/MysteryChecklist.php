<?php

namespace App\Models;


use App\Models\Traits\CompanyScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MysteryChecklist extends Model {
    use HasFactory, CompanyScoped;
    protected $fillable=['company_id','title','schema','is_active'];
    protected $casts = ['schema'=>'array','is_active'=>'boolean'];
}