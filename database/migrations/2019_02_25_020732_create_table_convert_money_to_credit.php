<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableConvertMoneyToCredit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convert_money_to_credit', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('credit')->default(0);
            $table->integer('price')->default(0);
            $table->integer('discount')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('convert_money_to_credit');
    }
}
