<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAlphabetTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alphabet_translations', function (Blueprint $table) {
            $table->longText('meaning')->nullable();
            $table->integer('alphabet_id')->unsigned();
            $table->string('locale');
            $table->unique(['locale', 'alphabet_id']);
            $table->foreign('alphabet_id')->references('id')->on('alphabet')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alphabet_translations');
    }
}
