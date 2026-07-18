<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableVocabularyTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vocabulary_translations', function (Blueprint $table) {
            $table->string('example1', 500)->nullable();
            $table->string('example2', 500)->nullable();
            $table->string('chinese_vietnamese_word')->nullable()->change();
            $table->string('meaning')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
