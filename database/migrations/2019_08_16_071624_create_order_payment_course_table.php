<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderPaymentCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_payment_course', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('classroom_id')->unsigned()->nullable();
            $table->integer('course_price_currency_id')->unsigned()->nullable();
            $table->integer('course_id')->unsigned()->nullable();
            $table->integer('test_id')->unsigned()->nullable();
            $table->integer('order_payment_id')->unsigned()->nullable();

            $table->string('class_name_original')->nullable();
            $table->integer('num_of_employee')->default(0);
            $table->integer('duration')->default(0);

            $table->foreign('classroom_id')->references('id')->on('classroom')->onDelete('set null');
            $table->foreign('course_price_currency_id')->references('id')->on('course_price_currency')->onDelete('set null');
            $table->foreign('course_id')->references('id')->on('course')->onDelete('set null');
            $table->foreign('test_id')->references('id')->on('test')->onDelete('set null');
            $table->foreign('order_payment_id')->references('id')->on('order_payment')->onDelete('cascade');
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
        Schema::dropIfExists('order_payment_course');
    }
}
