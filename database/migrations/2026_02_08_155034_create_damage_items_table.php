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
        Schema::create('damage_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->onDelete('cascade');
            $table->string('description'); // Description of damage
            $table->string('location')->nullable(); // Where on trailer
            $table->decimal('estimated_cost', 10, 2);
            $table->enum('severity', ['minor', 'moderate', 'major'])->default('minor');
            $table->boolean('repaired')->default(false);
            $table->date('repaired_at')->nullable();
            $table->text('repair_notes')->nullable();
            $table->timestamps();
            
            $table->index('inspection_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damage_items');
    }
};
