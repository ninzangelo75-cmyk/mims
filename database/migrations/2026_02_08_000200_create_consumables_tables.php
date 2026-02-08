<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumable_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('uom', 50);
            $table->timestamps();
        });

        Schema::create('inventories', function (Blueprint $table) {
            $table->foreignId('consumable_item_id')->primary()->constrained('consumable_items');
            $table->integer('qty_on_hand')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('consumable_items');
    }
};
