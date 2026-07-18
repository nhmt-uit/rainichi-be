<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGroupChapterTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_chapter_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_chapter_id')->unsigned();
            $table->string('locale');
            $table->string('name')->nullable();
            $table->unique(['group_chapter_id', 'locale']);
            $table->foreign('group_chapter_id')->references('id')->on('group_chapter')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_chapter_translations');
    }
}
