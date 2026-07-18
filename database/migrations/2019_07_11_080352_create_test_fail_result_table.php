<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestFailResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_fail_result', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('test_fail_id')->unsigned();
            $table->integer('test_result_id')->unsigned();
            $table->string('chapter_id')->nullable();
            $table->double('score')->default(0)->unsigned();
            $table->integer('question')->default(0)->unsigned();
            $table->double('total_score')->default(0)->unsigned();
            $table->integer('total_question')->default(0)->unsigned();

            $table->foreign('test_fail_id')->references('id')->on('test_fail')->onDelete('cascade');
            $table->foreign('test_result_id')->references('id')->on('test_result')->onDelete('cascade');
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
        Schema::dropIfExists('test_fail_result');
    }
}
