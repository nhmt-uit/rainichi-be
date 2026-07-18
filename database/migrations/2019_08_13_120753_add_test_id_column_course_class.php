<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTestIdColumnCourseClass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_class', function (Blueprint $table) {
            $table->integer('test_id')->unsigned()->nullable()->index();
            $table->foreign('test_id')->references('id')->on('test')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_class', function (Blueprint $table) {
            $table->dropColumn(['test_id']);
        });
    }
}
