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
        Schema::create('inspection_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->onDelete('cascade');
            $table->foreignId('damage_item_id')->nullable()->constrained()->onDelete('cascade'); // If photo is for specific damage
            $table->string('path'); // Storage path
            $table->string('disk')->default('local');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('inspection_id');
            $table->index('damage_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_photos');
    }
};
