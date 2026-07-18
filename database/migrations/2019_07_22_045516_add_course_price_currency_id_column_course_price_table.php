<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCoursePriceCurrencyIdColumnCoursePriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_price', function (Blueprint $table) {
            $table->integer('course_price_currency_id')->unsigned()->nullable();
            $table->foreign('course_price_currency_id')->references('id')->on('course_price_currency')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_price', function (Blueprint $table) {
            $table->dropForeign(['course_price_currency_id']);
            $table->dropColumn('course_price_currency_id');
        });
    }
}
