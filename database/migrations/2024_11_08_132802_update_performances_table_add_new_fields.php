<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('performances', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->integer('duration')->nullable();
        });
    }

    public function down()
    {
        Schema::table('performances', function (Blueprint $table) {
            $table->dropColumn(['title', 'image', 'duration']);
        });
    }
};