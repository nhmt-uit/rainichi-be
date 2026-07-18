<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCourseTypeTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_type_id')->unsigned();
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->string('locale')->index();

            $table->unique(['course_type_id', 'locale']);
            $table->foreign('course_type_id')->references('id')->on('course_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_type_translations');
    }
}
