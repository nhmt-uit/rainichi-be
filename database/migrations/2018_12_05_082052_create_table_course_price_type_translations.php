<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCoursePriceTypeTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_price_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_price_type_id')->unsigned();
            $table->string('name');
            $table->string('locale')->index();

            $table->foreign('course_price_type_id')->references('id')->on('course_price_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_price_type_translations');
    }
}
