<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop existing columns
            $table->dropColumn(['email', 'email_verified_at', 'name', 'remember_token']);
            
            // Rename id to useid
            $table->renameColumn('id', 'useid');
        });
        
        // Recreate table with new structure (MySQL doesn't support renameColumn easily)
        Schema::dropIfExists('users');
        
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('useid');
            $table->string('username', 100)->unique();
            $table->string('password', 255);
            $table->string('department', 150)->nullable();
            $table->string('designation', 150)->nullable();
            $table->string('fullname', 200);
            $table->string('contact', 50)->nullable();
            $table->string('emailadd', 150)->unique()->nullable();
            $table->enum('role', ['ADMIN', 'STAFF', 'USER'])->default('USER');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }
};
