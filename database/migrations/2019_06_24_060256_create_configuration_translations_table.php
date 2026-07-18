<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigurationTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuration_translations', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('configuration_id')->unsigned();
            $table->string('locale')->index();
            $table->string('title');
            $table->string('slogan')->nullable();
            $table->string('keywords')->nullable();
            $table->string('description')->nullable();
            $table->unique(['configuration_id', 'locale']);
            $table->foreign('configuration_id')->references('id')->on('configuration')->onDelete('cascade');
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
        Schema::dropIfExists('configuration_translations');
    }
}
