<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 1: Employees
// database/migrations/2024_01_02_000000_create_employees_table.php
return new class extends Migration {
    public function up() {
        Schema::create('employees', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->string('first_name');
            $t->string('last_name');
            $t->string('email')->nullable()->unique();
            $t->string('position')->nullable();
            $t->enum('status', ['ACTIVE','INACTIVE'])->default('ACTIVE');
            $t->softDeletes();
            $t->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('employees'); }
};
