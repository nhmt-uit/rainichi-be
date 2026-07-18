<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStrengthDataTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('strength_data_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('strength_data_id')->unsigned();
            $table->foreign('strength_data_id')->references('id')->on('strength_data')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('content')->nullable();
            $table->unique(['strength_data_id','locale']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('strength_data_translations');
    }
}
