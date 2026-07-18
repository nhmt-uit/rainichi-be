<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventDayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_day', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('credits');
            $table->integer('group_id')->unsigned();
            $table->integer('type');
            $table->foreign('group_id')->references('id')->on('group_user')->onDelete('cascade');
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
        Schema::dropIfExists('event_day');
    }
}
