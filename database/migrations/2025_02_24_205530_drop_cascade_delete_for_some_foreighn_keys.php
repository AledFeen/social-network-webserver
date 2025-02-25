<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropForeign(['comment_id']);
            $table->dropForeign(['message_id']);

            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('comment_id')->references('id')->on('comments');
            $table->foreign('message_id')->references('id')->on('messages');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropForeign(['comment_id']);
            $table->dropForeign(['message_id']);

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
        });
    }
};
