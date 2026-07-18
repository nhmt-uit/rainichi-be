<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGrammarTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grammar_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('grammar_id')->unsigned();
            $table->longText('name')->nullable();
            $table->string('locale');
            $table->unique(['locale', 'grammar_id']);
            $table->foreign('grammar_id')->references('id')->on('grammar')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grammar_translations');
    }
}
