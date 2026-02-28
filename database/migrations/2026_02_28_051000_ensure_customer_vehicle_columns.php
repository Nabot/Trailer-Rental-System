<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds car_registration, vehicle_make, vehicle_model if missing (e.g. after DB restore).
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'car_registration')) {
                $table->string('car_registration', 50)->nullable()->after('driver_licence');
            }
            if (!Schema::hasColumn('customers', 'vehicle_make')) {
                $table->string('vehicle_make', 100)->nullable()->after('car_registration');
            }
            if (!Schema::hasColumn('customers', 'vehicle_model')) {
                $table->string('vehicle_model', 100)->nullable()->after('vehicle_make');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('customers', 'car_registration')) {
                $columns[] = 'car_registration';
            }
            if (Schema::hasColumn('customers', 'vehicle_make')) {
                $columns[] = 'vehicle_make';
            }
            if (Schema::hasColumn('customers', 'vehicle_model')) {
                $columns[] = 'vehicle_model';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
