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
        Schema::create('releasing_ris', function (Blueprint $table) {
            $table->bigIncrements('rel_ris_id');
            $table->unsignedBigInteger('req_ris');
            $table->decimal('release_qty', 12, 2);
            $table->unsignedBigInteger('releaseby');
            $table->timestamp('releaseat')->useCurrent();
            
            $table->foreign('req_ris')->references('req_ris')->on('request_ris')->onDelete('restrict');
            $table->foreign('releaseby')->references('useid')->on('users')->onDelete('restrict');
            $table->index(['req_ris', 'releaseat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releasing_ris');
    }
};
