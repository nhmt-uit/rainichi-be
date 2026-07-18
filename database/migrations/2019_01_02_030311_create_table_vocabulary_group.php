<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableVocabularyGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vocabulary_group', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_chapter_id')->unsigned();
            $table->integer('vocabulary_id')->unsigned();
            $table->foreign('group_chapter_id')->references('id')->on('group_chapter')->onDelete('cascade');
            $table->foreign('vocabulary_id')->references('id')->on('vocabulary')->onDelete('cascade');
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
        Schema::dropIfExists('vocabulary_group');
    }
}
