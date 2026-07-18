<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLandingContentTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_content_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('landing_content_id')->unsigned();
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->string('sub_title')->nullable();
            $table->string('start_date')->nullable();
            $table->string('time_range')->nullable();
            $table->string('address')->nullable();
            $table->text('short_content')->nullable();
            $table->longText('content')->nullable();
            $table->unique(['landing_content_id', 'locale']);
            $table->foreign('landing_content_id')->references('id')->on('landing_content')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landing_content_translations');
    }
}
