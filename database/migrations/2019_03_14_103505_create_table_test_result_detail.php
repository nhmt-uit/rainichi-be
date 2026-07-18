<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTestResultDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_result_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_result_id');
            $table->integer('test_time_id');
            $table->integer('score')->unsigned();
            $table->integer('total_question')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_result_detail');
    }
}
