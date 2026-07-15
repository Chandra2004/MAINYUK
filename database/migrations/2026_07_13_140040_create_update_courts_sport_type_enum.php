<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Add new values to ENUM first, keeping old ones
            DB::statement("ALTER TABLE courts MODIFY COLUMN sport_type ENUM('futsal', 'bulutangkis', 'badminton', 'basket', 'tenis', 'padel', 'mini_soccer', 'billiard', 'voli') NOT NULL");
        }
        
        // Convert old data
        DB::table('courts')->where('sport_type', 'bulutangkis')->update(['sport_type' => 'badminton']);

        if (DB::getDriverName() === 'mysql') {
            // Remove old values from ENUM
            DB::statement("ALTER TABLE courts MODIFY COLUMN sport_type ENUM('futsal', 'badminton', 'basket', 'tenis', 'padel', 'mini_soccer', 'billiard', 'voli') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Temporarily allow both 'badminton' and 'bulutangkis'
            DB::statement("ALTER TABLE courts MODIFY COLUMN sport_type ENUM('futsal', 'badminton', 'bulutangkis', 'basket', 'tenis', 'padel', 'mini_soccer', 'billiard') NOT NULL");
        }

        // Revert old data if possible
        DB::table('courts')->where('sport_type', 'badminton')->update(['sport_type' => 'bulutangkis']);
        
        if (DB::getDriverName() === 'mysql') {
            // Revert to the original enum values
            DB::statement("ALTER TABLE courts MODIFY COLUMN sport_type ENUM('futsal', 'bulutangkis', 'basket', 'tenis', 'padel') NOT NULL");
        }
    }
};
