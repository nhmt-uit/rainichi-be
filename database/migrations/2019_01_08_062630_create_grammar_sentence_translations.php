<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrammarSentenceTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grammar_sentence_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->longText('content')->nullable();
            $table->longText('example')->nullable();
            $table->string('locale');
            $table->integer('grammar_sentence_id')->unsigned();
            $table->unique(['locale', 'grammar_sentence_id']);
            $table->foreign('grammar_sentence_id')->references('id')->on('grammar_sentence')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grammar_sentence_translations');
    }
}
