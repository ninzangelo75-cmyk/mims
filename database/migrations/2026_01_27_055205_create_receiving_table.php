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
        Schema::create('receiving', function (Blueprint $table) {
            $table->bigIncrements('recid');
            $table->unsignedBigInteger('itemcode');
            $table->timestamp('datereceived')->useCurrent();
            $table->string('supplier', 200)->nullable();
            $table->string('referenceno', 150)->nullable();
            $table->decimal('qty', 12, 2);
            $table->string('uom', 50);
            $table->decimal('unitprice', 12, 2);
            $table->decimal('totalamount', 14, 2);
            $table->string('batchno', 100);
            $table->date('expirydate');
            $table->string('department', 150)->nullable();
            $table->timestamps();
            
            $table->foreign('itemcode')->references('itemcode')->on('items')->onDelete('restrict');
            $table->index(['itemcode', 'batchno']);
            $table->index('expirydate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receiving');
    }
};
