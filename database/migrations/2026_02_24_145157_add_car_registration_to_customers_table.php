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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('car_registration', 50)->nullable()->after('driver_licence');
            $table->string('vehicle_make', 100)->nullable()->after('car_registration');
            $table->string('vehicle_model', 100)->nullable()->after('vehicle_make');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['car_registration', 'vehicle_make', 'vehicle_model']);
        });
    }
};
