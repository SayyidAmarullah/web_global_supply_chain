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
        // Update the ENUM column to include importer and exporter
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user', 'importer', 'exporter') DEFAULT 'importer'");
        
        // Update existing standard users to importers by default
        DB::table('users')->where('role', 'user')->update(['role' => 'importer']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->whereIn('role', ['importer', 'exporter'])->update(['role' => 'user']);
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user') DEFAULT 'user'");
    }
};
