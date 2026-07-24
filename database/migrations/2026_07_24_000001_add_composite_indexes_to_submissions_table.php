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
        Schema::table('submissions', function (Blueprint $table) {
            // Optimize leaderboard queries that filter by team_id + is_correct and sort by created_at
            $table->index(['team_id', 'is_correct', 'created_at']);
            // Optimize challenge solve count queries
            $table->index(['challenge_id', 'is_correct', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex(['team_id', 'is_correct', 'created_at']);
            $table->dropIndex(['challenge_id', 'is_correct', 'created_at']);
        });
    }
};
