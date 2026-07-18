<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableVocabularyTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vocabulary_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vocabulary_id')->unsigned();
            $table->string('chinese_vietnamese_word');
            $table->string('meaning');
            $table->string('audio_example_1', 500);
            $table->string('audio_example_2', 500);
            $table->string('locale')->index();

            $table->unique(['vocabulary_id','locale']);
            $table->foreign('vocabulary_id')->references('id')->on('vocabulary')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vocabulary_translations');
    }
}
