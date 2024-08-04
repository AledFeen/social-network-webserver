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
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('blocked_id')->change();

            $table->index('id');
            $table->index('user_id');
            $table->index('blocked_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blocked_users', function (Blueprint $table) {

            $table->integer('user_id')->change();
            $table->integer('blocked_id')->change();

            $table->dropIndex(['id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['blocked_id']);
        });
    }
};
