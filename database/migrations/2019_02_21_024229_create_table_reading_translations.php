<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReadingTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reading_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reading_id')->unsigned();
            $table->foreign('reading_id')->references('id')->on('reading')->onDelete('cascade');
            $table->string('locale');
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reading_translations');
    }
}
