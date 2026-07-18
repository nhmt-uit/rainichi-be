<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderPaymentIdColumnCourseClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_class', function (Blueprint $table) {
            $table->integer('order_payment_id')->unsigned()->nullable();
            $table->dateTime('approved_at')->nullable()->change();
            $table->foreign('order_payment_id')->references('id')->on('order_payment')->onDelete('cascade');
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
            $table->dropForeign(['order_payment_id']);
            $table->dropColumn(['order_payment_id']);
        });
    }
}
