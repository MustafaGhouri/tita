<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// Phase 3: Diagnostic
// database/migrations/2024_01_04_000000_create_test_templates_table.php
return new class extends Migration {
    public function up(){
        Schema::create('test_templates', function(Blueprint $t){
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->json('schema');
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('test_templates'); }
};
