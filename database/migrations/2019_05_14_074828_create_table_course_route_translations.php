<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCourseRouteTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_route_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_route_id')->unsigned();
            $table->string('image')->nullable();
            $table->string('locale')->index();
            $table->unique(['course_route_id','locale']);
            $table->foreign('course_route_id')->references('id')->on('course_route')->onDelete('cascade');
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
        Schema::dropIfExists('course_route_translations');
    }
}
