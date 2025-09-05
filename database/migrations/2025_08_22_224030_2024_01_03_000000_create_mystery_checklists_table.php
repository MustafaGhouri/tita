<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 2: Mystery Shopper
// database/migrations/2024_01_03_000000_create_mystery_checklists_table.php
return new class extends Migration {
    public function up(){
        Schema::create('mystery_checklists', function(Blueprint $t){
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->json('schema'); // dynamic items
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('mystery_checklists'); }
};
