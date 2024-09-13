<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompositionsTable extends Migration
{
    public function up()
    {
        Schema::create('compositions', function (Blueprint $table) {
            $table->unsignedBigInteger('id_performance');
            $table->unsignedBigInteger('id_actor');
            $table->timestamps();

            $table->foreign('id_performance')->references('id')->on('performances');
            $table->foreign('id_actor')->references('id')->on('actors');
        });
    }

    public function down()
    {
        Schema::dropIfExists('compositions');
    }
}
