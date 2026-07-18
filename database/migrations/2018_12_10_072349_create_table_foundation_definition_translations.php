<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFoundationDefinitionTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foundation_definition_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('foundation_id')->unsigned();
            $table->longText('hiragana_definition');
            $table->longText('hiragana_write');
            $table->longText('hiragana_read');
            $table->longText('katakana_definition');
            $table->longText('katakana_write');
            $table->longText('katakana_read');
            $table->string('locale');
            $table->unique(['locale', 'foundation_id']);
            $table->foreign('foundation_id')->references('id')->on('foundation_definition')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_foundation_definition_translations');
    }
}
