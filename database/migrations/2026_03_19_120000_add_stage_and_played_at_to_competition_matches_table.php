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
        Schema::table('competition_matches', function (Blueprint $table) {
            $table->string('stage', 30)->default('group')->after('away_team_id');
            $table->timestamp('played_at')->nullable()->after('away_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_matches', function (Blueprint $table) {
            $table->dropColumn(['stage', 'played_at']);
        });
    }
};
