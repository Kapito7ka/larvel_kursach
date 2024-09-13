<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->date('date');
            $table->time('time');
            $table->unsignedBigInteger('id_performance');
            $table->unsignedBigInteger('id_hall');
            $table->unsignedBigInteger('id_user');
            $table->timestamps();

            $table->foreign('id_performance')->references('id')->on('performances');
            $table->foreign('id_hall')->references('id')->on('halls');
            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
