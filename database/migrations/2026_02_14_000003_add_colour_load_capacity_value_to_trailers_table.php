<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trailers', function (Blueprint $table) {
            $table->string('colour')->nullable()->after('registration_number');
            $table->unsignedInteger('load_capacity_kg')->nullable()->after('colour')->comment('Max load capacity in kg');
            $table->decimal('trailer_value', 12, 2)->nullable()->after('load_capacity_kg')->comment('Replacement/value in N$');
        });
    }

    public function down(): void
    {
        Schema::table('trailers', function (Blueprint $table) {
            $table->dropColumn(['colour', 'load_capacity_kg', 'trailer_value']);
        });
    }
};
