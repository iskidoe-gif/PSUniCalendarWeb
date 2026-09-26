<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_requests', function (Blueprint $table) {
            $table->unsignedTinyInteger('sdg_number')->nullable()->after('campus');
            $table->text('planning_note')->nullable()->after('status');
            $table->string('google_event_id')->nullable()->after('planning_note');
        });
    }

    public function down(): void
    {
        Schema::table('event_requests', function (Blueprint $table) {
            $table->dropColumn(['sdg_number', 'planning_note', 'google_event_id']);
        });
    }
};