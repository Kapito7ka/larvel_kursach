<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformancesTable extends Migration
{
    public function up()
    {
        Schema::create('performances', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('genres');
            $table->time('time');
            $table->integer('duration');
            $table->decimal('price', 10, 2);
            $table->string('producer');
            $table->string('hole_plains')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('performances');
    }
}
