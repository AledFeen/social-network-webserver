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
        Schema::create('notification_comment_replies', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('comment_id')->index();
            $table->bigInteger('reply_id')->index();
            $table->bigInteger('user_id')->index();

            $table->foreign('reply_id')->references('id')->on('comments')->onDelete('cascade');
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_comment_replies');
    }
};
