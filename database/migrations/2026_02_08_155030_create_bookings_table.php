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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('booking_number')->unique(); // TRL-2026-0001 format
            $table->foreignId('trailer_id')->constrained()->onDelete('restrict');
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // Staff who created
            $table->enum('status', ['draft', 'pending', 'confirmed', 'active', 'returned', 'cancelled'])->default('draft');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('pickup_time')->nullable();
            $table->integer('total_days'); // Calculated days (inclusive)
            $table->decimal('rate_per_day', 10, 2); // Snapshot of rate at booking time
            $table->decimal('rental_cost', 10, 2); // days * rate_per_day
            $table->decimal('required_deposit', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('straps_fee', 10, 2)->default(0);
            $table->decimal('damage_waiver_fee', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2); // rental_cost + addons
            $table->decimal('total_amount', 10, 2); // Final total
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance', 10, 2); // total_amount - paid_amount
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            
            $table->index('booking_number');
            $table->index('trailer_id');
            $table->index('customer_id');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
            // Prevent overlapping bookings (enforced at application level with DB locks)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
