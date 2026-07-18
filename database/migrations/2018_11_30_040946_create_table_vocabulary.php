<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableVocabulary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vocabulary', function (Blueprint $table) {
            $table->increments('id');
            $table->string('vocabulary');
            $table->string('image')->nullable();
            $table->string('svg')->nullable();
            $table->string('audio')->nullable();
            $table->string('hiragana')->nullable();
            $table->string('kanji')->nullable();
            $table->string('katakana')->nullable();
            $table->string('spelling')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vocabulary');
    }
}
