<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNullableFieldsInPerformancesTable extends Migration
{
    public function up()
    {
        Schema::table('performances', function (Blueprint $table) {
            $table->string('producer')->nullable()->change(); // Make producer nullable
        });
    }

    public function down()
    {
        Schema::table('performances', function (Blueprint $table) {
            $table->string('producer')->nullable(false)->change(); // Revert to non-nullable in down method if needed
        });
    }
}
