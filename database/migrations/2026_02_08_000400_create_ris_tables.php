<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ris_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ris_no')->unique();
            $table->text('purpose');
            $table->string('status')->default('DRAFT')->index();
            $table->foreignId('created_by_user_id')->constrained('users', 'useid');
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users', 'useid');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ris_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ris_request_id')->constrained('ris_requests')->cascadeOnDelete();
            $table->foreignId('consumable_item_id')->constrained('consumable_items');
            $table->integer('qty_requested');
            $table->integer('qty_issued')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ris_lines');
        Schema::dropIfExists('ris_requests');
    }
};
