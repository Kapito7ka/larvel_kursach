<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->integer('age');
            $table->string('phone_number')->nullable();
            $table->string('email')->unique();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('id_history')->nullable();
            $table->timestamps();

            // Define foreign key constraints if necessary
            $table->foreign('id_history')->references('id')->on('history')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
