<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestResultDetailQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_result_detail_question', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('result_detail_id')->unsigned()->index();
            $table->integer('question_id')->unsigned()->index();
            $table->boolean('status')->default(0);
            $table->unique(['result_detail_id', 'question_id']);
            $table->foreign('question_id')->references('id')->on('question')->onDelete('cascade');
            $table->foreign('result_detail_id')->references('id')->on('test_result_detail')->onDelete('cascade');
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
        Schema::dropIfExists('test_result_detail_question');
    }
}
