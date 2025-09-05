<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// database/migrations/2024_01_01_000000_create_companies_table.php
return new class extends Migration {
    public function up() {
        Schema::create('companies', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('companies'); }
};
