<?php


// app/Models/Traits/CompanyScoped.php
namespace App\Models\Traits;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait CompanyScoped {
    protected static function bootCompanyScoped() {
        static::addGlobalScope('companies', function(Builder $q){
            // if (Auth::check()) {
            // $q->where($q->getModel()->getTable().'.company_id', Auth::user()->company_id);
            // }
        });
        static::creating(function($model){
            if (Auth::check() && empty($model->company_id)) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }
}
