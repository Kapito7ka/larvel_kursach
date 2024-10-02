<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Перейменування стовпця 'name' на 'full_name'
            $table->renameColumn('name', 'full_name');

            // Додаємо нові стовпці
            $table->integer('age')->nullable();
            $table->string('phone_numbers', 20)->nullable();
            $table->string('status')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Повертаємо 'full_name' назад на 'name'
            $table->renameColumn('full_name', 'name');

            // Видаляємо додані стовпці
            $table->dropColumn('age');
            $table->dropColumn('phone_numbers');
            $table->dropColumn('status');
        });
    }
};
