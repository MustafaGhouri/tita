<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(){
        Schema::create('diagnostic_results', function(Blueprint $t){
            $t->id();
            $t->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $t->json('answers');
            $t->float('score')->nullable();
            $t->timestamp('submitted_at');
            $t->float('manual_score')->nullable();
            $t->unique('employee_id'); // one-time
        });
    }
    public function down(){ Schema::dropIfExists('diagnostic_results'); }
};
