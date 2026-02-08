<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ptr_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ptr_no')->unique();
            $table->string('status')->default('DRAFT')->index();

            $table->foreignId('asset_id')->constrained('assets');
            $table->foreignId('from_user_id')->constrained('users', 'useid');
            $table->foreignId('to_user_id')->constrained('users', 'useid');

            $table->text('reason');

            $table->foreignId('created_by_user_id')->constrained('users', 'useid');
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users', 'useid');
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users', 'useid');

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ptr_requests');
    }
};
