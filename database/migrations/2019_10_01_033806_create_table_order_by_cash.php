<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrderByCash extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_by_cash', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->unsigned();
            $table->integer('course_id')->unsigned()->nullable();
            $table->integer('test_id')->unsigned()->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_provider')->nullable();
            $table->string('payment_transaction_id')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_msg')->nullable();
            $table->string('discount_code')->nullable();
            $table->double('discount_amount')->default(0);
            $table->double('amount')->default(0);
            $table->double('final_amount')->nullable();
            $table->foreign('course_id')->references('id')->on('course')->onDelete('cascade');
            $table->foreign('test_id')->references('id')->on('test')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('order_by_cash');
    }
}
