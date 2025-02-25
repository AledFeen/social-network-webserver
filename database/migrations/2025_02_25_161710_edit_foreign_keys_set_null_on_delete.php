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

            $table->unsignedBigInteger('post_id')->nullable()->change();
            $table->unsignedBigInteger('comment_id')->nullable()->change();
            $table->unsignedBigInteger('message_id')->nullable()->change();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('set null');
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('set null');
            $table->foreign('message_id')->references('id')->on('messages')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropForeign(['comment_id']);
            $table->dropForeign(['message_id']);

            $table->unsignedBigInteger('post_id')->nullable(false)->change();
            $table->unsignedBigInteger('comment_id')->nullable(false)->change();
            $table->unsignedBigInteger('message_id')->nullable(false)->change();

            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('comment_id')->references('id')->on('comments');
            $table->foreign('message_id')->references('id')->on('messages');
        });
    }
};
