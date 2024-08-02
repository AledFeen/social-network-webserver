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
        Schema::table('accounts', function (Blueprint $table) {

            $table->string('real_name', 64)->nullable();
            $table->string('location', 64)->nullable();

            $table->dropColumn('name');
            $table->dropColumn('surname');
            $table->dropColumn('second_name');

            $table->foreign('location')->references('name')->on('locations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {

            $table->dropForeign(['location']);

            $table->dropColumn('real_name');
            $table->dropColumn('location');

            $table->string('name', 24)->nullable();
            $table->string('surname', 24)->nullable();
            $table->string('second_name', 24)->nullable();
        });
    }
};
