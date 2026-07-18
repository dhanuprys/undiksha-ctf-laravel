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
        Schema::create('challenge_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamps();
        });

        Schema::table('challenges', function (Blueprint $table) {
            $table->dropColumn('attachment_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challenges', function (Blueprint $table) {
            $table->string('attachment_path')->nullable()->after('flag');
        });

        Schema::dropIfExists('challenge_attachments');
    }
};
