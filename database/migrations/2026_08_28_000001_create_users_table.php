<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  if (!Schema::hasTable('users')) Schema::create('users', function(Blueprint $t){$t->id();$t->string('username',100)->unique();$t->string('email')->unique();$t->string('password');$t->string('role',30)->default('USER');$t->rememberToken();$t->timestamps();});
  else { Schema::table('users', function(Blueprint $t){ if(!Schema::hasColumn('users','username')) $t->string('username',100)->nullable()->unique(); if(!Schema::hasColumn('users','role')) $t->string('role',30)->default('USER'); if(!Schema::hasColumn('users','remember_token')) $t->rememberToken(); if(!Schema::hasColumn('users','created_at')) $t->timestamps(); }); }
 }
 public function down(): void { Schema::dropIfExists('users'); }
};
