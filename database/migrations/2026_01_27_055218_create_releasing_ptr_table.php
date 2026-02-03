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
        Schema::create('releasing_ptr', function (Blueprint $table) {
            $table->bigIncrements('rel_ptr_id');
            $table->string('ptr_no', 100);
            $table->decimal('release_qty', 12, 2);
            $table->unsignedBigInteger('releaseby');
            $table->timestamp('releaseat')->useCurrent();
            
            $table->foreign('ptr_no')->references('ptr_no')->on('request_ptr')->onDelete('restrict');
            $table->foreign('releaseby')->references('useid')->on('users')->onDelete('restrict');
            $table->index(['ptr_no', 'releaseat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releasing_ptr');
    }
};
