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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['pickup', 'return']); // Pre-pickup or return inspection
            $table->json('checklist')->nullable(); // JSON checklist items with status
            $table->text('notes')->nullable();
            $table->text('condition_notes')->nullable();
            $table->boolean('is_damaged')->default(false);
            $table->decimal('total_damage_cost', 10, 2)->default(0); // Sum of damage items
            $table->foreignId('inspected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('inspected_at')->nullable();
            $table->timestamps();
            
            $table->index('booking_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
