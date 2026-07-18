<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCoursePrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_price', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_price_type_id')->unsigned();
            $table->integer('course_id')->unsigned();
            $table->integer('buying_credits');
            $table->integer('reward_credits');
            $table->boolean('is_active');
            $table->foreign('course_price_type_id')->references('id')->on('course_price_type')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('course')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_price');
    }
}
