<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_course', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('title', 50);
            $table->time('starttime');
            $table->integer('day');
            $table->integer('orderby');
            $table->enum('valid', ['Y', 'N']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_course');
    }
}
