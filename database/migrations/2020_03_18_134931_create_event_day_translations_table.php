<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventDayTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_day_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_day_id')->unsigned();
            $table->string('locale')->index();
            $table->string('name')->nullable();
            $table->foreign('event_day_id')->references('id')->on('event_day')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_day_translations');
    }
}
