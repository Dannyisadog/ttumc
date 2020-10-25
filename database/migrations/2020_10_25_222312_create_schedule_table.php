<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 50);
            $table->enum('orderby', ['user', 'band', 'admin'])->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('band_id')->nullable();
            $table->integer('course_id')->nullable();
            $table->dateTime('starttime');
            $table->timestamp('createtime')->useCurrent();
            $table->enum('valid', ['Y', 'N'])->default('Y');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule');
    }
}
