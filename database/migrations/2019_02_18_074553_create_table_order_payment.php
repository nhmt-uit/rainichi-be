<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrderPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_payment', function (Blueprint $table) {
            $table->increments('id');
            $table->string('payment_method')->nullable();
            $table->double('amount')->nullable();
            $table->integer('credit')->default(0);
            $table->integer('user_id')->unsigned();
            $table->double('final_amount')->nullable();
            $table->string('payment_provider')->nullable();
            $table->string('payment_transaction_id')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_msg')->nullable();
            $table->string('discount_code')->nullable();
            $table->string('discount_amount')->nullable();
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
        Schema::dropIfExists('order_payment');
    }
}
