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
        Schema::create('request_ris', function (Blueprint $table) {
            $table->bigIncrements('req_ris');
            $table->string('ris_no', 100)->unique();
            $table->string('division', 150)->nullable();
            $table->string('department', 150)->nullable();
            $table->unsignedBigInteger('itemcode');
            $table->decimal('req_qty', 12, 2);
            $table->boolean('isavailable')->default(false);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('requestedby')->nullable();
            $table->unsignedBigInteger('approvedby')->nullable();
            $table->unsignedBigInteger('receivedby')->nullable();
            $table->timestamp('requestedat')->nullable();
            $table->timestamp('approvedat')->nullable();
            $table->timestamp('receivedat')->nullable();
            $table->timestamps();
            
            $table->foreign('itemcode')->references('itemcode')->on('items')->onDelete('restrict');
            $table->foreign('requestedby')->references('useid')->on('users')->onDelete('set null');
            $table->index(['itemcode', 'isavailable']);
            $table->index('requestedat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_ris');
    }
};
