<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseLessonFollowingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_lesson_following', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_lesson_id')->unsigned();
            $table->integer('following_course_lesson_id')->unsigned();
            $table->foreign('course_lesson_id')->references('id')->on('course_lesson')->onDelete('cascade');
            $table->foreign('following_course_lesson_id')->references('id')->on('course_lesson')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_lesson_following');
    }
}
