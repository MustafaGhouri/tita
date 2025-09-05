<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/2024_01_03_010000_create_mystery_evaluations_table.php
return new class extends Migration {
    public function up(){
        Schema::create('mystery_evaluations', function(Blueprint $t){
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $t->foreignId('checklist_id')->constrained('mystery_checklists')->cascadeOnDelete();
            $t->string('monthKey'); // YYYY-MM
            $t->json('answers');
            $t->float('score')->nullable();
            $t->string('video_path')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->unique(['employee_id','monthKey']);
        });
    }
    public function down(){ Schema::dropIfExists('mystery_evaluations'); }
};
