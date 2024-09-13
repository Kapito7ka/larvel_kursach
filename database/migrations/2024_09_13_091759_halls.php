<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHallsTable extends Migration
{
    public function up()
    {
        Schema::create('halls', function (Blueprint $table) {
            $table->id();
            $table->integer('hall_number');
            $table->unsignedBigInteger('id_place');
            $table->timestamps();

            $table->foreign('id_place')->references('id')->on('places');
        });
    }

    public function down()
    {
        Schema::dropIfExists('halls');
    }
}
