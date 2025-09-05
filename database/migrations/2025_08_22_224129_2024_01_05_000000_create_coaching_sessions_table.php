<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(){
        Schema::create('coaching_sessions', function(Blueprint $t){
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $t->date('date');
            $t->json('observations')->nullable();
            $t->json('recommendations')->nullable();
            $t->date('follow_up_date')->nullable();
            $t->json('attachments')->nullable(); // array of paths
            $t->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $t->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('coaching_sessions'); }
};
