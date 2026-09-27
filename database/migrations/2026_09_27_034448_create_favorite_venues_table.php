<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorite_venues', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('venue_name');
            $table->string('campus')->nullable();
            $table->timestamps();

            $table->unique(['email', 'venue_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorite_venues');
    }
};
