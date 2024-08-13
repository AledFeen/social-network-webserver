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
        Schema::table('privacy_settings', function (Blueprint $table) {
            $table->dropIndex(['id']);
            $table->dropIndex(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('privacy_settings', function (Blueprint $table) {
            $table->index(['id']);
            $table->index(['user_id']);
        });
    }
};
