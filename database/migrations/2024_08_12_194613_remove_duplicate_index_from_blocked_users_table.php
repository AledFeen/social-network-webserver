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
        Schema::table('blocked_users', function (Blueprint $table) {
            $table->dropIndex(['id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['blocked_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blocked_users', function (Blueprint $table) {
            $table->index('id');
            $table->index('user_id');
            $table->index('blocked_id');
        });
    }
};
