<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_class', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('course_id')->unsigned()->nullable()->index();
            $table->integer('classroom_id')->unsigned()->nullable()->index();
            $table->boolean('is_active')->default(1);
            $table->foreign('course_id')->references('id')->on('course')->onDelete('cascade');
            $table->foreign('classroom_id')->references('id')->on('classroom')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_class');
    }
}
