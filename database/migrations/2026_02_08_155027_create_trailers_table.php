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
        Schema::create('trailers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // e.g., "Single Axle", "Double Axle"
            $table->string('axle'); // "Single" or "Double"
            $table->decimal('size_m', 5, 2); // Size in meters
            $table->decimal('rate_per_day', 10, 2); // Daily rental rate
            $table->decimal('required_deposit', 10, 2)->nullable(); // Deposit amount (nullable, can use global setting)
            $table->enum('status', ['available', 'maintenance', 'unavailable'])->default('available');
            $table->text('description')->nullable();
            $table->string('registration_number')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index('status');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trailers');
    }
};
