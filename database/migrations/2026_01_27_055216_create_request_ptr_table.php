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
        Schema::create('request_ptr', function (Blueprint $table) {
            $table->bigIncrements('req_ptr');
            $table->string('ptr_no', 100)->unique();
            $table->string('division', 150)->nullable();
            $table->string('target', 150)->nullable();
            $table->enum('trans_type', ['Donation', 'Reassignment', 'Relocate', 'Others']);
            $table->string('trans_type_other', 150)->nullable();
            $table->unsignedBigInteger('itemcode');
            $table->decimal('req_qty', 12, 2);
            $table->text('remarks')->nullable();
            $table->text('purpose')->nullable();
            $table->unsignedBigInteger('approvedby')->nullable();
            $table->unsignedBigInteger('receivedby')->nullable();
            $table->timestamp('requestedat')->nullable();
            $table->timestamp('approvedat')->nullable();
            $table->timestamp('receivedat')->nullable();
            $table->timestamps();
            
            $table->foreign('itemcode')->references('itemcode')->on('items')->onDelete('restrict');
            $table->index(['itemcode', 'trans_type']);
            $table->index('requestedat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_ptr');
    }
};
