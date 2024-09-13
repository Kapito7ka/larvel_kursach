<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_ticket');
            $table->unsignedBigInteger('id_user');
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('id_discount')->nullable();
            $table->timestamps();

            $table->foreign('id_ticket')->references('id')->on('tickets');
            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_discount')->references('id')->on('discounts');
        });
    }

    public function down()
    {
        Schema::dropIfExists('history');
    }
}
