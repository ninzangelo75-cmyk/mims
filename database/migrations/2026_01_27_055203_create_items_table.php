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
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('itemcode');
            $table->string('itemname', 200);
            $table->text('description')->nullable();
            $table->string('brand', 200)->nullable();
            $table->string('dosage_form', 100)->nullable();
            $table->string('strength', 100)->nullable();
            $table->string('default_uom', 50)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
