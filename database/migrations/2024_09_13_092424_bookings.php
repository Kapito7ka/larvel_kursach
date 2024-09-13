<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->string('ticket_number')->primary();
            $table->timestamps();

            $table->foreign('ticket_number')->references('ticket_number')->on('tickets');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
