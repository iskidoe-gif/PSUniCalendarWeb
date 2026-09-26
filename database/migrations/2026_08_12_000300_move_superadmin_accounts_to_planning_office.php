<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'superadmin')->update(['role' => 'planning_office']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'planning_office')->update(['role' => 'superadmin']);
    }
};