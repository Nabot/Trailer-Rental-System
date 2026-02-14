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
        Schema::create('trailer_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trailer_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'registration', 'roadworthy', etc.
            $table->string('name');
            $table->string('path'); // Storage path
            $table->string('disk')->default('local');
            $table->date('expiry_date')->nullable(); // For documents with expiry
            $table->date('reminder_date')->nullable(); // When to send reminder
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('trailer_id');
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trailer_documents');
    }
};
