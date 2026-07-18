<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGrammarGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grammar_group', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_chapter_id')->unsigned();
            $table->integer('grammar_id')->unsigned();
            $table->foreign('group_chapter_id')->references('id')->on('group_chapter')->onDelete('cascade');
            $table->foreign('grammar_id')->references('id')->on('grammar')->onDelete('cascade');
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
        Schema::dropIfExists('grammar_group');
    }
}
