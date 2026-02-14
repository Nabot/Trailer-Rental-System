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
        Schema::create('trailer_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trailer_id')->constrained()->onDelete('cascade');
            $table->string('path'); // Storage path
            $table->string('disk')->default('local'); // Storage disk
            $table->integer('order')->default(0); // Display order
            $table->boolean('is_primary')->default(false); // Primary photo
            $table->timestamps();
            
            $table->index('trailer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trailer_photos');
    }
};
