<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKanjiTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kanji_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kanji_id')->unsigned();
            $table->string('meaning')->nullable();
            $table->string('chinese_vietnamese_word')->nullable();
            $table->string('example1', 500)->nullable();
            $table->string('example2', 500)->nullable();
            $table->string('locale')->index();
            $table->unique(['kanji_id', 'locale']);
            $table->foreign('kanji_id')->references('id')->on('kanji')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kanji_translations');
    }
}
