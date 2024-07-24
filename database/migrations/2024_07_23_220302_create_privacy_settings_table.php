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
        Schema::create('privacy_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unique();
            $table->enum('account_type', ['public', 'private'])->default('public');
            $table->enum('who_can_comment', ['all', 'only_subscribers', 'none'])->default('all');
            $table->enum('who_can_repost', ['all', 'only_subscribers', 'none'])->default('all');
            $table->enum('who_can_message', ['all', 'only_subscribers', 'only_my_subscriptions', 'none'])->default('all');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('privacy_settings');
    }
};
