<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// database/migrations/2024_01_01_000100_update_users_for_roles_company.php
return new class extends Migration {
    public function up() {
        Schema::table('users', function (Blueprint $t) {
            $t->foreignId('company_id')->after('id')->constrained()->cascadeOnDelete();
            $t->enum('role', ['CLIENT','EMPLOYEE'])->default('EMPLOYEE')->after('email');
            $t->string('position')->nullable();
        });
    }
    public function down() {
        Schema::table('users', function (Blueprint $t) {
            $t->dropConstrainedForeignId('company_id');
            $t->dropColumn(['role','position']);
        });
    }
};
