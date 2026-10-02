<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Who the event is for: false = only the campus where it's held,
     * true = all campuses (e.g. a University Meet held at Lingayen).
     * Separate from `campus`, which stays the venue's campus.
     */
    public function up(): void
    {
        Schema::table('event_requests', function (Blueprint $table) {
            $table->boolean('is_university_wide')->default(false)->after('campus');
        });
    }

    public function down(): void
    {
        Schema::table('event_requests', function (Blueprint $table) {
            $table->dropColumn('is_university_wide');
        });
    }
};
