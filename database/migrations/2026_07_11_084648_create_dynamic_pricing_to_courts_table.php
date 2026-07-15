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
        Schema::table('courts', function (Blueprint $table) {
            $table->decimal('peak_hour_multiplier', 4, 2)->default(1.0)->after('price_per_hour');
            $table->time('peak_hour_start')->nullable()->after('peak_hour_multiplier');
            $table->time('peak_hour_end')->nullable()->after('peak_hour_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courts', function (Blueprint $table) {
            $table->dropColumn(['peak_hour_multiplier', 'peak_hour_start', 'peak_hour_end']);
        });
    }
};
